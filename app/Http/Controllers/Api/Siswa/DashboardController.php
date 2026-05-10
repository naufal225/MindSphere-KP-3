<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Services\StudentReflectionTemplateService;
use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\ParentSupport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private StudentReflectionTemplateService $reflectionTemplateService)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $profile = [
            'name' => $user->name,
            'message' => 'Keep growing every day!!',
            'xp' => $user->xp,
            'coin' => $user->coin,
        ];

        $habitCount = HabitLog::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $challengeCount = ChallengeParticipant::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $reflectionCount = $this->reflectionTemplateService->countTotalReflections($user);
        $streak = $this->calculateProgressStreak($user->id, $today);

        $latestHabits = Habit::select('habits.id', 'habits.title', 'habits.period')
            ->with([
                'logs' => function ($query) use ($user, $today, $startOfWeek, $endOfWeek) {
                    $query->where('user_id', $user->id)
                        ->where(function ($builder) use ($today, $startOfWeek, $endOfWeek) {
                            $builder->whereDate('date', $today)
                                ->orWhereBetween('date', [$startOfWeek, $endOfWeek]);
                        })
                        ->select('id', 'habit_id', 'status', 'date');
                },
            ])
            ->whereNotIn('habits.id', function ($query) use ($user, $today, $startOfWeek, $endOfWeek) {
                $query->select('habit_id')
                    ->from('habit_logs')
                    ->where('user_id', $user->id)
                    ->where('status', 'completed')
                    ->where(function ($builder) use ($today, $startOfWeek, $endOfWeek) {
                        $builder->whereDate('date', $today)
                            ->orWhereBetween('date', [$startOfWeek, $endOfWeek]);
                    });
            })
            ->where(function ($query) use ($today) {
                $query->where('habits.end_date', '>=', $today)
                    ->where('habits.start_date', '<=', $today);
            })
            ->orderBy('habits.created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($habit) use ($today, $startOfWeek, $endOfWeek) {
                $isCompleted = false;

                if ($habit->period === 'daily') {
                    $isCompleted = $habit->logs->contains(function ($log) use ($today) {
                        return $log->date->format('Y-m-d') === $today->format('Y-m-d')
                            && $log->status === 'completed';
                    });
                } elseif ($habit->period === 'weekly') {
                    $isCompleted = $habit->logs->contains(function ($log) use ($startOfWeek, $endOfWeek) {
                        return $log->date->between($startOfWeek, $endOfWeek)
                            && $log->status === 'completed';
                    });
                }

                return [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'period' => $habit->period,
                    'is_done_today' => $isCompleted,
                ];
            });

        $latestChallenges = Challenge::whereNotIn('id', function ($query) use ($user) {
                $query->select('challenge_id')
                    ->from('challenge_participants')
                    ->where('user_id', $user->id)
                    ->where('status', 'completed');
            })
            ->where('end_date', '>=', $today)
            ->where('start_date', '<=', $today)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'xp_reward', 'start_date', 'end_date'])
            ->map(function ($challenge) use ($user) {
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

        $reflectionStatus = $this->reflectionTemplateService->getDashboardReflectionStatus($user);
        $parentSupportData = $this->getParentSupportData($user->id);

        return response()->json([
            'status' => 'success',
            'data' => [
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
            ],
        ]);
    }

    private function calculateProgressStreak(int $userId, Carbon $today): int
    {
        $habitDates = HabitLog::where('user_id', $userId)
            ->where('status', 'completed')
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $challengeDates = ChallengeParticipant::where('user_id', $userId)
            ->where('status', 'completed')
            ->pluck('updated_at')
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $student = User::findOrFail($userId);
        $reflectionDates = $this->reflectionTemplateService->getActivityDates($student);

        $allDates = $habitDates
            ->merge($challengeDates)
            ->merge($reflectionDates)
            ->unique()
            ->filter()
            ->values();

        if (!$allDates->contains($today->toDateString())) {
            return 0;
        }

        $consecutive = 0;
        $cursor = $today->copy();

        while ($allDates->contains($cursor->toDateString())) {
            $consecutive++;
            $cursor->subDay();
        }

        return max(0, $consecutive - 1);
    }

    private function getParentSupportData(int $studentId): array
    {
        $student = User::find($studentId);
        $hasParent = $student && $student->parent_id;

        if (!$hasParent) {
            return [
                'has_parent' => false,
                'has_support' => false,
                'latest_support' => null,
                'unread_count' => 0,
                'message' => 'Belum ada orang tua yang terhubung. Silakan hubungi admin atau guru untuk menghubungkan akun dengan orang tua.',
            ];
        }

        $latestSupport = ParentSupport::with('parent')
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->first();

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
                    ? "Kamu memiliki {$unreadCount} pesan dukungan baru dari orang tua."
                    : 'Terima kasih sudah berbagi progress dengan orang tua.',
            ];
        }

        return [
            'has_parent' => true,
            'has_support' => false,
            'latest_support' => null,
            'unread_count' => 0,
            'message' => 'Orang tua kamu siap memberikan dukungan.',
        ];
    }
}
