<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class HabitSubmissionController extends Controller
{
    public function getHabitDetail($habitId)
    {
        $user = Auth::user();

        $habit = Habit::with(['category', 'assignedBy', 'creator'])
            ->withCount(['logs as total_logs'])
            ->find($habitId);

        if (!$habit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Habit tidak ditemukan'
            ], 404);
        }

        $userLogs = HabitLog::where('user_id', $user->id)
            ->where('habit_id', $habitId)
            ->orderBy('date', 'desc')
            ->get();

        $completedLogs = $userLogs->where('status', 'completed')->count();
        $submittedLogs = $userLogs->where('status', 'submitted')->count();
        $totalUserLogs = $userLogs->count();
        $completionRate = $totalUserLogs > 0 ? round(($completedLogs / $totalUserLogs) * 100) : 0;

        $todayLog = HabitLog::where('user_id', $user->id)
            ->where('habit_id', $habitId)
            ->whereDate('date', today())
            ->first();

        $habitData = [
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
            'creator_name' => optional($habit->creator)->name,
            'is_owner' => $habit->created_by === $user->id,
            'is_assigned_to_me' => $habit->assigned_by === $user->id,
            'total_logs' => $habit->total_logs,
            'user_progress' => [
                'total_logs' => $totalUserLogs,
                'completed_logs' => $completedLogs,
                'submitted_logs' => $submittedLogs,
                'completion_rate' => $completionRate,
                'streak' => $this->calculateStreak($userLogs),
                'last_activity' => $userLogs->first()->date ?? null,
            ],
            'today_log' => $todayLog ? [
                'id' => $todayLog->id,
                'status' => $todayLog->status,
                'note' => $todayLog->note,
                'proof_url' => $todayLog->proof_url,
                'date' => $todayLog->date
            ] : null
        ];

        return response()->json([
            'status' => 'success',
            'data' => $habitData
        ]);
    }

    public function joinHabit($habitId)
    {
        $user = Auth::user();

        $habit = Habit::find($habitId);
        if (!$habit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Habit tidak ditemukan'
            ], 404);
        }

        // Cek apakah user sudah join
        $existingLog = HabitLog::where('user_id', $user->id)
            ->where('habit_id', $habitId)
            ->whereDate('date', today())
            ->first();

        if ($existingLog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah bergabung dengan habit ini hari ini'
            ], 400);
        }

        try {
            $log = HabitLog::create([
                'habit_id' => $habitId,
                'user_id' => $user->id,
                'date' => today(),
                'status' => 'joined'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil bergabung dengan habit',
                'data' => $log
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal bergabung dengan habit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submitProof(Request $request, $habitId)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $log = HabitLog::where('user_id', $user->id)
            ->where('habit_id', $habitId)
            ->whereDate('date', today())
            ->first();

        if (!$log) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda belum bergabung dengan habit ini hari ini'
            ], 404);
        }

        if ($log->status === 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Habit ini sudah diselesaikan'
            ], 400);
        }

        try {
            // Upload image
            if ($request->hasFile('proof_image')) {
                $imagePath = $request->file('proof_image')->store('habit_proofs', 'public');

                $log->update([
                    'proof_url' => $imagePath,
                    'status' => 'submitted',
                    'note' => $request->note
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Bukti berhasil dikirim, menunggu verifikasi',
                'data' => $log
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim bukti',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getHabitLogs($habitId)
    {
        $user = Auth::user();

        $logs = HabitLog::where('user_id', $user->id)
            ->where('habit_id', $habitId)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'date' => $log->date->format('Y-m-d'),
                    'status' => $log->status,
                    'note' => $log->note,
                    'proof_url' => $log->proof_url,
                    'created_at' => $log->created_at
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $logs
        ]);
    }

    public function getTodayHabits()
    {
        $user = Auth::user();

        $habits = Habit::with(['category'])
            ->where(function($query) use ($user) {
                $query->where('assigned_by', $user->id)
                      ->orWhere('created_by', $user->id);
            })
            ->get()
            ->map(function ($habit) use ($user) {
                $todayLog = HabitLog::where('user_id', $user->id)
                    ->where('habit_id', $habit->id)
                    ->whereDate('date', today())
                    ->first();

                return [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'category' => $habit->category->name,
                    'xp_reward' => $habit->xp_reward,
                    'period' => $habit->period->value,
                    'today_status' => $todayLog ? $todayLog->status : 'not_joined',
                    'today_note' => $todayLog ? $todayLog->note : null,
                    'proof_url' => $todayLog ? $todayLog->proof_url : null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $habits
        ]);
    }

    private function calculateStreak($logs)
    {
        $completedLogs = $logs->where('status', 'completed')
            ->sortByDesc('date')
            ->values();

        if ($completedLogs->isEmpty()) {
            return 0;
        }

        $streak = 1;
        $currentDate = $completedLogs->first()->date;

        foreach ($completedLogs->skip(1) as $log) {
            $previousDate = $currentDate->copy()->subDay();
            if ($log->date->format('Y-m-d') === $previousDate->format('Y-m-d')) {
                $streak++;
                $currentDate = $log->date;
            } else {
                break;
            }
        }

        return $streak;
    }
}
