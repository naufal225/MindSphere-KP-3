<?php

namespace App\Http\Controllers\Api\Ortu;

use App\Http\Controllers\Controller;
use App\Http\Services\LevelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ChildProgressController extends Controller
{
    public function getChildProgress($childId)
    {
        try {
            $parent = Auth::user();

            // Validasi bahwa user adalah orang tua
            if ($parent->role !== 'ortu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Hanya untuk role orang tua.'
                ], 403);
            }

            // Validasi bahwa anak memang milik orang tua ini
            $child = User::with(['classAsStudent'])
                ->where('id', $childId)
                ->where('parent_id', $parent->id)
                ->where('role', 'siswa')
                ->first();

            if (!$child) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anak tidak ditemukan'
                ], 404);
            }

            // Data dasar anak
            $className = 'Belum ada kelas';
            if ($child->classAsStudent->isNotEmpty()) {
                $class = $child->classAsStudent->first();
                $className = $class->name;
            }

            // Hitung XP untuk progress bar
            $currentLevel = $child->level;
            $currentXp = $child->xp;
            $xpForNextLevel = LevelService::getXpForNextLevel($child->level + 1);
            $xpProgress = min(($currentXp / $xpForNextLevel) * 100, 100);

            // Data aktivitas 7 hari terakhir
            $weeklyActivity = $this->getWeeklyActivity($child->id);

            // Data ringkasan
            $summary = $this->getWeeklySummary($child->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'child' => [
                        'id' => $child->id,
                        'name' => $child->name,
                        'class' => $className,
                        'level' => $currentLevel,
                        'xp' => $currentXp,
                        'xp_for_next_level' => $xpForNextLevel,
                        'xp_progress' => round($xpProgress),
                        'avatar_url' => $child->avatar_url,
                    ],
                    'weekly_activity' => $weeklyActivity,
                    'summary' => $summary,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan progress semua anak (untuk segmented control)
     */
    public function getAllChildrenProgress()
    {
        try {
            $parent = Auth::user();

            if ($parent->role !== 'ortu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Hanya untuk role orang tua.'
                ], 403);
            }

            // Ambil semua anak dengan data dasar
            $children = User::with(['classAsStudent'])
                ->where('parent_id', $parent->id)
                ->where('role', 'siswa')
                ->get()
                ->map(function($child) {
                    $className = 'Belum ada kelas';
                    if ($child->classAsStudent->isNotEmpty()) {
                        $class = $child->classAsStudent->first();
                        $className = $class->name;
                    }

                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'class' => $className,
                        'level' => $child->level,
                        'xp' => $child->xp,
                        'avatar_url' => $child->avatar_url,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'children' => $children
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghitung XP yang dibutuhkan untuk level berikutnya
     */
    private function calculateXpForNextLevel($currentLevel)
    {
        // Formula: 1000 XP untuk level 1, tambah 500 setiap level
        return 1000 + (($currentLevel - 1) * 500);
    }

    /**
     * Mendapatkan data aktivitas 7 hari terakhir
     */
    private function getWeeklyActivity($studentId)
    {
        $activityData = [];
        $startDate = now()->subDays(6)->startOfDay(); // 7 hari termasuk hari ini

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');

            // Hitung total habit logs untuk hari tersebut (status completed)
            $habitCount = DB::table('habit_logs')
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->whereDate('date', $dateString)
                ->count();

            // Hitung total challenge participants untuk hari tersebut (status completed)
            $challengeCount = DB::table('challenge_participants')
                ->where('user_id', $studentId)
                ->where('status', 'completed')
                ->whereDate('created_at', $dateString)
                ->count();

            // Hitung total reflections untuk hari tersebut
            $reflectionCount = DB::table('reflections')
                ->where('user_id', $studentId)
                ->whereDate('created_at', $dateString)
                ->count();

            // Total aktivitas (bisa disesuaikan bobotnya)
            $totalActivity = $habitCount + $challengeCount + $reflectionCount;

            $activityData[] = [
                'date' => $dateString,
                'day' => $date->format('D'), // Mon, Tue, etc
                'day_short' => $date->format('d M'), // 15 Jan
                'habit_count' => $habitCount,
                'challenge_count' => $challengeCount,
                'reflection_count' => $reflectionCount,
                'total_activity' => $totalActivity,
                'activity_percentage' => min(($totalActivity / 5) * 100, 100) // Asumsi max 5 aktivitas per hari
            ];
        }

        return $activityData;
    }

    /**
     * Mendapatkan ringkasan mingguan
     */
    private function getWeeklySummary($studentId)
    {
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        // Total habits completed dalam 7 hari
        $habitsCompleted = DB::table('habit_logs')
            ->where('user_id', $studentId)
            ->where('status', 'completed')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->count();

        // Total challenges completed dalam 7 hari
        $challengesCompleted = DB::table('challenge_participants')
            ->where('user_id', $studentId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Total reflections dibuat dalam 7 hari
        $reflectionsCreated = DB::table('reflections')
            ->where('user_id', $studentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Total habits yang diharapkan (asumsi 3 habits per hari)
        $expectedHabits = 3 * 7;

        return [
            'habits_completed' => $habitsCompleted,
            'habits_expected' => $expectedHabits,
            'habits_percentage' => $expectedHabits > 0 ? min(($habitsCompleted / $expectedHabits) * 100, 100) : 0,
            'challenges_completed' => $challengesCompleted,
            'reflections_created' => $reflectionsCreated,
            'total_xp_earned' => $this->getWeeklyXp($studentId, $startDate, $endDate),
        ];
    }

    /**
     * Mendapatkan XP yang diperoleh dalam minggu ini
     */
    private function getWeeklyXp($studentId, $startDate, $endDate)
    {
        // XP dari habits (asumsi 10 XP per habit)
        $habitXp = DB::table('habit_logs')
            ->where('user_id', $studentId)
            ->where('status', 'completed')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->count() * 10;

        // XP dari challenges (asumsi 50 XP per challenge)
        $challengeXp = DB::table('challenge_participants')
            ->where('user_id', $studentId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count() * 50;

        // XP dari reflections (asumsi 20 XP per reflection)
        $reflectionXp = DB::table('reflections')
            ->where('user_id', $studentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count() * 20;

        return $habitXp + $challengeXp + $reflectionXp;
    }
}
