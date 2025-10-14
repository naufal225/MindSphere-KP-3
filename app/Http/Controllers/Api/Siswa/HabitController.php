<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Habit;
use App\Models\HabitLog;
use Carbon\Carbon;

class HabitController extends Controller
{
    /**
     * GET /api/siswa/habits
     * Ambil semua habit untuk siswa login
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Optional: filter period (daily/weekly)
        $period = $request->query('period');

        $habits = Habit::with(['category'])
            ->orderBy('created_at', 'desc')
            ->when($period, fn ($q) => $q->where('period', $period))
            ->get()
            ->map(function ($habit) use ($user, $today) {
                $todayLog = HabitLog::where('habit_id', $habit->id)
                    ->where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->first();

                $totalLogs = HabitLog::where('habit_id', $habit->id)
                    ->where('user_id', $user->id)
                    ->where('status', 'done')
                    ->count();

                return [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'description' => $habit->description,
                    'category' => optional($habit->category)->name,
                    'period' => $habit->period,
                    'is_done_today' => $todayLog?->status === 'done',
                    'total_completed' => $totalLogs,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $habits
        ]);
    }
}
