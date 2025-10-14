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

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); // pastikan siswa sudah login dengan sanctum/jwt
        $today = Carbon::today();

        // --- 1. Data Dasar Siswa ---
        $profile = [
            'name' => $user->name,
            'message' => "Keep growing every day!!",
            'xp' => $user->xp,
        ];

        // --- 2. Statistik Habit, Challenge, Reflection ---
        $habitCount = HabitLog::where('user_id', $user->id)
            ->where('status', 'done')
            ->count();

        $challengeCount = ChallengeParticipant::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $reflectionCount = Reflection::where('user_id', $user->id)->count();

        // Hitung streak (berapa hari berturut-turut habit dilakukan)
        $streak = $this->calculateHabitStreak($user->id);

        // --- 3. 5 Habit Terbaru ---
        $latestHabits = Habit::select('habits.id', 'habits.title')
            ->with(['logs' => function ($q) use ($user, $today) {
                $q->where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->select('id', 'habit_id', 'status');
            }])
            ->orderBy('habits.created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($habit) {
                return [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'is_done_today' => optional($habit->logs->first())->status === 'done'
                ];
            });

        // --- 4. 5 Challenge Terbaru ---
        $latestChallenges = Challenge::orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'xp_reward'])
            ->map(function ($challenge) {
                return [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'xp_reward' => $challenge->xp_reward,
                ];
            });

        // --- 5. Refleksi Hari Ini ---
        $todayReflection = Reflection::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $reflectionStatus = $todayReflection
            ? ['status' => 'sudah', 'message' => 'Kamu sudah menulis refleksi hari ini 📝']
            : ['status' => 'belum', 'message' => 'Yuk tulis refleksi hari ini agar tetap sadar diri 🌱'];

        // --- 6. 3 Postingan Komunitas Terbaru ---
        $latestPosts = ForumPost::withCount('comments')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get(['id', 'title'])
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'comments_count' => $post->comments_count,
                ];
            });

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
            'community_feed' => $latestPosts,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Hitung streak habit (berapa hari berturut-turut user melakukan habit)
     */
    private function calculateHabitStreak($userId)
    {
        $dates = HabitLog::where('user_id', $userId)
            ->where('status', 'done')
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        if ($dates->isEmpty()) return 0;

        $streak = 1;
        for ($i = 0; $i < $dates->count() - 1; $i++) {
            $current = Carbon::parse($dates[$i]);
            $next = Carbon::parse($dates[$i + 1]);
            if ($current->diffInDays($next) == 1) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }
}
