<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use App\Models\Reflection;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ParentSupport;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // --- 1. Data Dasar Siswa ---
        $profile = [
            'name' => $user->name,
            'message' => "Keep growing every day!!",
            'xp' => $user->xp,
            'coin' => $user->coin,
        ];

        // --- 2. Statistik Habit, Challenge, Reflection ---
        $habitCount = HabitLog::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $challengeCount = ChallengeParticipant::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $reflectionCount = Reflection::where('user_id', $user->id)->count();

        // Hitung streak (berdasarkan aktivitas habit/challenge/refleksi)
        $streak = $this->calculateProgressStreak($user->id, $today);

        // --- 3. 5 Habit Terbaru (Sesuai periode dan belum selesai) ---
        $latestHabits = Habit::select('habits.id', 'habits.title', 'habits.period')
            ->with([
                'logs' => function ($q) use ($user, $today, $startOfWeek, $endOfWeek) {
                    $q->where('user_id', $user->id)
                        ->where(function($query) use ($today, $startOfWeek, $endOfWeek) {
                            $query->whereDate('date', $today) // Untuk daily
                                  ->orWhereBetween('date', [$startOfWeek, $endOfWeek]); // Untuk weekly
                        })
                        ->select('id', 'habit_id', 'status', 'date');
                }
            ])
            ->whereNotIn('habits.id', function($query) use ($user, $today, $startOfWeek, $endOfWeek) {
                $query->select('habit_id')
                      ->from('habit_logs')
                      ->where('user_id', $user->id)
                      ->where('status', 'completed')
                      ->where(function($q) use ($today, $startOfWeek, $endOfWeek) {
                          // Untuk daily: cek hari ini
                          $q->whereDate('date', $today)
                            // Untuk weekly: cek minggu ini
                            ->orWhereBetween('date', [$startOfWeek, $endOfWeek]);
                      });
            })
            ->where(function($query) use ($today) {
                $query->where('habits.end_date', '>=', $today)
                      ->where('habits.start_date', '<=', $today);
            })
            ->orderBy('habits.created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($habit) use ($today, $startOfWeek, $endOfWeek) {
                $isCompleted = false;

                // Cek berdasarkan periode
                if ($habit->period === 'daily') {
                    $todayLog = $habit->logs->first(function ($log) use ($today) {
                        return $log->date->format('Y-m-d') === $today->format('Y-m-d') &&
                               $log->status === 'completed';
                    });
                    $isCompleted = !is_null($todayLog);
                } else if ($habit->period === 'weekly') {
                    $weekLog = $habit->logs->first(function ($log) use ($startOfWeek, $endOfWeek) {
                        return $log->date->between($startOfWeek, $endOfWeek) &&
                               $log->status === 'completed';
                    });
                    $isCompleted = !is_null($weekLog);
                }

                return [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'period' => $habit->period,
                    'is_done_today' => $isCompleted
                ];
            });

        // --- 4. 5 Challenge Terbaru (Hanya yang belum completed) ---
        $latestChallenges = Challenge::whereNotIn('id', function($query) use ($user) {
                $query->select('challenge_id')
                      ->from('challenge_participants')
                      ->where('user_id', $user->id)
                      ->where('status', 'completed');
            })
            ->where('end_date', '>=', $today) // Hanya challenge yang masih aktif
            ->where('start_date', '<=', $today)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'xp_reward', 'start_date', 'end_date'])
            ->map(function ($challenge) use ($user) {
                // Cek status participation
                $participation = ChallengeParticipant::where('challenge_id', $challenge->id)
                    ->where('user_id', $user->id)
                    ->first();

                return [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'xp_reward' => $challenge->xp_reward,
                    'start_date' => $challenge->start_date->format('d M Y'),
                    'end_date' => $challenge->end_date->format('d M Y'),
                    'participation_status' => $participation ? $participation->status : 'not_joined',
                    'is_joined' => !is_null($participation),
                    'is_completed' => $participation && $participation->status === 'completed',
                ];
            });

        // --- 5. Refleksi Hari Ini ---
        $todayReflection = Reflection::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $reflectionStatus = $todayReflection
            ? ['status' => 'sudah', 'message' => 'Kamu sudah menulis refleksi hari ini 📝']
            : ['status' => 'belum', 'message' => 'Yuk tulis refleksi hari ini agar tetap sadar diri 🌱'];

        // --- 6. Parent Support Data ---
        $parentSupportData = $this->getParentSupportData($user->id);

        // --- Gabungkan semua ---
        $data = [
            'profile' => $profile,
            'stats' => [
                'habits_done' => $habitCount,
                'challenges_completed' => $challengeCount,
                'reflections_total' => $reflectionCount,
                'streak' => $streak,
            ],
            'latest_habits' => $latestHabits,
            'latest_challenges' => $latestChallenges,
            'reflection_today' => $reflectionStatus,
            'parent_support' => $parentSupportData,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Hitung streak aktivitas (habit completed, challenge completed, refleksi dibuat).
     * Basis perhitungan: hari ini, putus jika ada sehari tanpa aktivitas.
     * Rumus strike: jika 2 hari berturut, 1; 3 hari -> 2; 4 hari -> 3; dst (streak = max(0, consecutive-1)).
     */
    private function calculateProgressStreak($userId, Carbon $today)
    {
        // Habit progress dates (completed)
        $habitDates = HabitLog::where('user_id', $userId)
            ->where('status', 'completed')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString());

        // Challenge progress dates (completed)
        $challengeDates = ChallengeParticipant::where('user_id', $userId)
            ->where('status', 'completed')
            ->pluck('updated_at')
            ->map(fn($d) => Carbon::parse($d)->toDateString());

        // Reflection dates (any submission counted)
        $reflectionDates = Reflection::where('user_id', $userId)
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString());

        $allDates = $habitDates
            ->merge($challengeDates)
            ->merge($reflectionDates)
            ->unique()
            ->filter() // remove null
            ->values();

        // Harus punya aktivitas hari ini, jika tidak streak = 0
        if (!$allDates->contains($today->toDateString())) {
            return 0;
        }

        $consecutive = 0;
        $cursor = $today->copy();
        while ($allDates->contains($cursor->toDateString())) {
            $consecutive++;
            $cursor->subDay();
        }

        // Mapping: 1 hari -> 0, 2 hari -> 1, 3 hari -> 2, dst
        return max(0, $consecutive - 1);
    }

    /**
     * Ambil data Parent Support untuk siswa
     */
    private function getParentSupportData($studentId)
    {
        // Cek apakah siswa memiliki orang tua dengan mengecek parent_id
        $student = User::find($studentId);
        $hasParent = $student && $student->parent_id;

        if (!$hasParent) {
            return [
                'has_parent' => false,
                'has_support' => false,
                'latest_support' => null,
                'unread_count' => 0,
                'message' => 'Belum ada orang tua yang terhubung. Silakan hubungi admin atau guru untuk menghubungkan akun dengan orang tua.'
            ];
        }

        // Ambil data orang tua
        $parent = User::find($student->parent_id);

        // Ambil pesan parent support terbaru
        $latestSupport = ParentSupport::with('parent')
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Hitung pesan yang belum dibaca
        $unreadCount = ParentSupport::where('student_id', $studentId)
            ->whereNull('read_at')
            ->count();

        if ($latestSupport) {
            return [
                'has_parent' => true,
                'has_support' => true,
                'latest_support' => [
                    'id' => $latestSupport->id,
                    'message' => $latestSupport->message,
                    'parent_name' => $latestSupport->parent->name,
                    'created_at' => $latestSupport->created_at->format('d M Y H:i'),
                    'time_ago' => $latestSupport->created_at->diffForHumans(),
                    'is_read' => !is_null($latestSupport->read_at),
                ],
                'unread_count' => $unreadCount,
                'message' => $unreadCount > 0
                    ? "Kamu memiliki {$unreadCount} pesan dukungan baru dari orang tua ✨"
                    : "Terima kasih sudah berbagi progress dengan orang tua! 👨‍👩‍👧‍👦"
            ];
        } else {
            return [
                'has_parent' => true,
                'has_support' => false,
                'latest_support' => null,
                'unread_count' => 0,
                'message' => 'Orang tua kamu siap memberikan dukungan! 💝'
            ];
        }
    }
}
