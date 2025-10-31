<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Enums\HabitType;
use App\Enums\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class HabitController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        $user = Auth::user();
        $status = $request->query('status');

        $habits = Habit::with(['category', 'assignedBy'])
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->withCount(['logs as total_logs'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($habit) use ($user) {
                $userLogs = HabitLog::where('user_id', $user->id)
                    ->where('habit_id', $habit->id)
                    ->get();

                $completedLogs = $userLogs->where('status', 'completed')->count();
                $submittedLogs = $userLogs->where('status', 'submitted')->count();
                $totalUserLogs = $userLogs->count();
                $completionRate = $totalUserLogs > 0 ? round(($completedLogs / $totalUserLogs) * 100) : 0;

                // Get today's log
                $todayLog = HabitLog::where('user_id', $user->id)
                    ->where('habit_id', $habit->id)
                    ->whereDate('date', today())
                    ->first();

                return [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'description' => $habit->description,
                    'category' => optional($habit->category)->name,
                    'category_id' => $habit->category_id,
                    'xp_reward' => $habit->xp_reward,
                    'type' => $habit->type->value,
                    'period' => $habit->period->value,
                    'assigned_by' => $habit->assigned_by,
                    'assigned_by_name' => optional($habit->assignedBy)->name,
                    'created_by' => $habit->created_by,
                    'is_owner' => $habit->created_by === $user->id,
                    'is_assigned_to_me' => $habit->assigned_by === $user->id,
                    'total_logs' => $habit->total_logs,
                    'user_progress' => [
                        'total_logs' => $totalUserLogs,
                        'completed_logs' => $completedLogs,
                        'submitted_logs' => $submittedLogs,
                        'completion_rate' => $completionRate,
                        'last_activity' => $userLogs->sortByDesc('date')->first()->date ?? null,
                    ],
                    'today_log' => $todayLog ? [
                        'id' => $todayLog->id,
                        'status' => $todayLog->status,
                        'note' => $todayLog->note,
                        'proof_url' => $todayLog->proof_url,
                        'date' => $todayLog->date,
                    ] : null
                ];
            });

        if ($status) {
            $habits = $habits->filter(function($habit) use ($status) {
                if ($status === 'completed') {
                    return $habit['user_progress']['completion_rate'] == 100;
                } elseif ($status === 'active') {
                    return $habit['user_progress']['completion_rate'] < 100;
                }
                return true;
            })->values();
        }

        return response()->json([
            'status' => 'success',
            'data' => $habits
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'xp_reward' => 'required|integer|min:1|max:100',
            'period' => 'required|in:daily,weekly',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $habit = Habit::create([
                'title' => $request->title,
                'description' => $request->description,
                'type' => HabitType::SELF,
                'category_id' => $request->category_id,
                'xp_reward' => $request->xp_reward,
                'period' => Period::from($request->period),
                'assigned_by' => $user->id,
                'created_by' => $user->id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Habit berhasil dibuat',
                'data' => [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'description' => $habit->description,
                    'category' => optional($habit->category)->name,
                    'xp_reward' => $habit->xp_reward,
                    'period' => $habit->period->value,
                    'type' => $habit->type->value,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat habit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $habit = Habit::where('id', $id)
            ->where('created_by', $user->id)
            ->where('type', HabitType::SELF)
            ->first();

        if (!$habit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Habit tidak ditemukan atau tidak dapat diubah'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'xp_reward' => 'sometimes|integer|min:1|max:100',
            'period' => 'sometimes|in:daily,weekly',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = $request->only([
                'title',
                'description',
                'category_id',
                'xp_reward'
            ]);

            if ($request->has('period')) {
                $updateData['period'] = Period::from($request->period);
            }

            $habit->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Habit berhasil diupdate',
                'data' => [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'description' => $habit->description,
                    'category' => optional($habit->category)->name,
                    'xp_reward' => $habit->xp_reward,
                    'period' => $habit->period->value,
                    'type' => $habit->type->value,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate habit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $habit = Habit::where('id', $id)
            ->where('created_by', $user->id)
            ->where('type', HabitType::SELF)
            ->first();

        if (!$habit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Habit tidak ditemukan atau tidak dapat dihapus'
            ], 404);
        }

        try {
            $habit->logs()->delete();
            $habit->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Habit berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus habit',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
