<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\HabitLog;
use App\Models\ChallengeParticipant;
use App\Models\Reflection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class UserProgressService
{
    /**
     * Get user progress data with filters
     */
    public function getUserProgressData(array $filters = [])
    {
        try {
            $query = User::where('role', 'siswa')
                ->with([
                    'classAsStudent' => function ($query) {
                        $query->select('school_classes.id', 'school_classes.name');
                    }
                ])
                ->select([
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.level',
                    'users.xp',
                    'users.avatar_url',
                    'users.created_at'
                ]);

            // Apply filters
            $this->applyFilters($query, $filters);

            // Get base user data
            $users = $query->get();

            // if ($users->isEmpty()) {
            //     throw new Exception('Tidak ada data siswa yang ditemukan.');
            // }

            // Enhance with progress metrics
            return $this->enhanceWithProgressMetrics($users, $filters);

        } catch (Exception $e) {
            throw new Exception('Gagal mengambil data progress: ' . $e->getMessage());
        }
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters)
    {
        // Filter by class
        if (!empty($filters['class_id'])) {
            $query->whereHas('classAsStudent', function ($q) use ($filters) {
                $q->where('school_classes.id', $filters['class_id']);
            });
        }

        // Validate and set date range
        $startDate = $this->validateAndGetStartDate($filters);
        $endDate = $this->validateAndGetEndDate($filters);

        // Store dates for later use
        $filters['_start_date'] = $startDate;
        $filters['_end_date'] = $endDate;
    }

    /**
     * Validate and get start date
     */
    private function validateAndGetStartDate(array $filters): Carbon
    {
        if (!empty($filters['start_date'])) {
            $startDate = Carbon::parse($filters['start_date']);
            if ($startDate->isFuture()) {
                throw new Exception('Tanggal mulai tidak boleh di masa depan.');
            }
            return $startDate;
        }

        // Default to last 30 days
        return Carbon::now()->subDays(30);
    }

    /**
     * Validate and get end date
     */
    private function validateAndGetEndDate(array $filters): Carbon
    {
        if (!empty($filters['end_date'])) {
            $endDate = Carbon::parse($filters['end_date']);

            // Check if start date exists and validate range
            if (!empty($filters['start_date'])) {
                $startDate = Carbon::parse($filters['start_date']);
                if ($endDate->lessThan($startDate)) {
                    throw new Exception('Tanggal akhir tidak boleh sebelum tanggal mulai.');
                }
            }

            return $endDate;
        }

        return Carbon::now();
    }

    /**
     * Enhance users with progress metrics
     */
    private function enhanceWithProgressMetrics($users, array $filters)
    {
        try {
            $startDate = $filters['_start_date'] ?? Carbon::now()->subDays(30);
            $endDate = $filters['_end_date'] ?? Carbon::now();

            $enhancedData = $users->map(function ($user) use ($startDate, $endDate, $filters) {
                // Get habit metrics
                $habitMetrics = $this->getHabitMetrics($user->id, $startDate, $endDate);

                // Get challenge metrics
                $challengeMetrics = $this->getChallengeMetrics($user->id, $startDate, $endDate);

                // Get reflection metrics
                $reflectionMetrics = $this->getReflectionMetrics($user->id, $startDate, $endDate);

                // Calculate activity days
                $activityDays = $this->getActivityDays($user->id, $startDate, $endDate);

                // Calculate total activities for minimum activity filter
                $totalActivities = $habitMetrics['completed'] + $challengeMetrics['completed'] + $reflectionMetrics['count'];

                // Apply minimum activity filter
                if (isset($filters['min_activity']) && $filters['min_activity'] > 0 && $totalActivities < $filters['min_activity']) {
                    return null;
                }

                // Apply mood filter
                if (!empty($filters['mood_range']) && !$this->passesMoodFilter($reflectionMetrics['avg_mood_score'], $filters['mood_range'])) {
                    return null;
                }

                // Apply include inactive filter
                if (empty($filters['include_inactive']) && $totalActivities === 0) {
                    return null;
                }

                return [
                    'user' => $user,
                    'class_name' => $user->classAsStudent->first()?->name ?? 'Tidak ada kelas',
                    'level' => $user->level,
                    'xp_total' => $user->xp,
                    'habits_completed' => $habitMetrics['completed'],
                    'habit_streak' => $habitMetrics['streak'],
                    'challenges_completed' => $challengeMetrics['completed'],
                    'reflections_written' => $reflectionMetrics['count'],
                    'average_mood' => $reflectionMetrics['avg_mood'],
                    'average_mood_score' => $reflectionMetrics['avg_mood_score'],
                    'activity_days' => $activityDays,
                    'total_activities' => $totalActivities,
                ];
            })->filter()->values();

            // if ($enhancedData->isEmpty()) {
            //     throw new Exception('Tidak ada data yang sesuai dengan filter yang dipilih.');
            // }

            return $enhancedData;

        } catch (Exception $e) {
            throw new Exception('Gagal memproses metrik progress: ' . $e->getMessage());
        }
    }

    /**
     * Get habit metrics for a user
     */
    private function getHabitMetrics($userId, $startDate, $endDate)
    {
        try {
            $habitLogs = HabitLog::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $completed = $habitLogs->where('status', 'done')->count();
            $streak = $this->calculateHabitStreak($habitLogs);

            return [
                'completed' => $completed,
                'streak' => $streak,
            ];
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil data habits: ' . $e->getMessage());
        }
    }

    /**
     * Calculate habit streak
     */
    private function calculateHabitStreak($habitLogs)
    {
        try {
            $doneLogs = $habitLogs->where('status', 'done')
                ->sortBy('date')
                ->groupBy('date')
                ->keys();

            $maxStreak = 0;
            $currentStreak = 0;
            $previousDate = null;

            foreach ($doneLogs as $date) {
                $currentDate = Carbon::parse($date);

                if ($previousDate === null || $currentDate->diffInDays($previousDate) === 1) {
                    $currentStreak++;
                } else {
                    $currentStreak = 1;
                }

                $maxStreak = max($maxStreak, $currentStreak);
                $previousDate = $currentDate;
            }

            return $maxStreak;
        } catch (Exception $e) {
            throw new Exception('Gagal menghitung streak: ' . $e->getMessage());
        }
    }

    /**
     * Get challenge metrics for a user
     */
    private function getChallengeMetrics($userId, $startDate, $endDate)
    {
        try {
            $challenges = ChallengeParticipant::where('user_id', $userId)
                ->where('status', 'completed')
                ->whereBetween('submitted_at', [$startDate, $endDate])
                ->count();

            return [
                'completed' => $challenges,
            ];
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil data challenges: ' . $e->getMessage());
        }
    }

    /**
     * Get reflection metrics for a user
     */
    private function getReflectionMetrics($userId, $startDate, $endDate)
    {
        try {
            $reflections = Reflection::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $moodScores = [
                'happy' => 5,
                'neutral' => 4,
                'tired' => 3,
                'sad' => 2,
                'angry' => 1,
            ];

            $totalScore = 0;
            $count = $reflections->count();

            foreach ($reflections as $reflection) {
                $totalScore += $moodScores[$reflection->mood->value] ?? 3;
            }

            $avgScore = $count > 0 ? $totalScore / $count : 0;
            $avgMood = $this->getMoodFromScore($avgScore);

            return [
                'count' => $count,
                'avg_mood_score' => $avgScore,
                'avg_mood' => $avgMood,
            ];
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil data reflections: ' . $e->getMessage());
        }
    }

    /**
     * Convert mood score to mood label
     */
    private function getMoodFromScore($score)
    {
        if ($score >= 4.5)
            return 'Sangat Senang';
        if ($score >= 3.5)
            return 'Senang';
        if ($score >= 2.5)
            return 'Biasa';
        if ($score >= 1.5)
            return 'Sedih';
        return 'Sangat Sedih';
    }

    /**
     * Get activity days count
     */
    private function getActivityDays($userId, $startDate, $endDate)
    {
        try {
            $habitDays = HabitLog::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->distinct('date')
                ->pluck('date');

            $reflectionDays = Reflection::where('user_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->distinct('date')
                ->pluck('date');

            $allDays = $habitDays->merge($reflectionDays)->unique();

            return $allDays->count();
        } catch (Exception $e) {
            throw new Exception('Gagal menghitung hari aktif: ' . $e->getMessage());
        }
    }

    /**
     * Check if user passes mood filter
     */
    private function passesMoodFilter($avgMoodScore, $moodRange)
    {
        $ranges = [
            'happy-only' => [4.5, 5],
            'neutral+' => [3.5, 5],
            'all' => [1, 5],
        ];

        if (!isset($ranges[$moodRange]))
            return true;

        list($min, $max) = $ranges[$moodRange];
        return $avgMoodScore >= $min && $avgMoodScore <= $max;
    }

    /**
     * Get chart data for top 10 students by XP
     */
    public function getTopStudentsChartData(array $filters = [])
    {
        try {
            $data = $this->getUserProgressData($filters);

            return $data->sortByDesc('xp_total')
                ->take(10)
                ->map(function ($item) {
                    return [
                        'name' => $item['user']->name,
                        'xp' => $item['xp_total'],
                        'level' => $item['level'],
                    ];
                })->values();
        } catch (Exception $e) {
            throw new Exception('Gagal membuat chart top students: ' . $e->getMessage());
        }
    }

    /**
     * Get chart data for class comparison
     */
    public function getClassComparisonChartData(array $filters = [])
    {
        try {
            $data = $this->getUserProgressData($filters);

            $classData = $data->groupBy('class_name')->map(function ($classStudents, $className) {
                $totalStudents = $classStudents->count();
                $avgActivities = $classStudents->avg('total_activities');
                $avgXp = $classStudents->avg('xp_total');

                return [
                    'class_name' => $className,
                    'avg_activities' => round($avgActivities, 1),
                    'avg_xp' => round($avgXp, 1),
                    'student_count' => $totalStudents,
                ];
            });

            return $classData->sortByDesc('avg_activities')->values();
        } catch (Exception $e) {
            throw new Exception('Gagal membuat chart perbandingan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Get mood distribution data
     */
    public function getMoodDistributionData(array $filters = [])
    {
        try {
            $data = $this->getUserProgressData($filters);

            $moodDistribution = $data->groupBy('average_mood')->map(function ($students, $mood) {
                return [
                    'mood' => $mood,
                    'count' => $students->count(),
                    'percentage' => 0,
                ];
            });

            $total = $moodDistribution->sum('count');

            return $moodDistribution->map(function ($item) use ($total) {
                $item['percentage'] = $total > 0 ? round(($item['count'] / $total) * 100, 1) : 0;
                return $item;
            })->values();
        } catch (Exception $e) {
            throw new Exception('Gagal membuat chart distribusi mood: ' . $e->getMessage());
        }
    }

    /**
     * Get all classes for filter dropdown
     */
    public function getClassesForFilter()
    {
        try {
            return SchoolClass::withCount('students')
                ->orderBy('name')
                ->get(['id', 'name']);
        } catch (Exception $e) {
            throw new Exception('Gagal mengambil data kelas: ' . $e->getMessage());
        }
    }
}
