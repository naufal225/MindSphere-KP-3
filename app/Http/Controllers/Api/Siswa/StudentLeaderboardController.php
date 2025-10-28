<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use App\Enums\HabitStatus;
use App\Enums\ChallengeStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLeaderboardController extends Controller
{
    /**
     * Get leaderboard for students in the same class
     */
    public function index(Request $request)
    {
        $siswa = Auth::user();

        if ($siswa->role !== 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya siswa yang dapat mengakses.'
            ], 403);
        }

        // Dapatkan kelas siswa
        $kelas = $siswa->classAsStudent()->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak terdaftar di kelas manapun.'
            ], 404);
        }

        // Dapatkan semua siswa di kelas yang sama, termasuk siswa yang login
        $students = $kelas->students()
            ->where('users.role', 'siswa')
            ->select('users.*')
            ->withCount([
                'habitLogs as habits_completed_count' => function ($query) {
                    $query->where('status', HabitStatus::COMPLETED);
                },
                'challengeParticipants as challenges_completed_count' => function ($query) {
                    $query->where('status', ChallengeStatus::COMPLETED);
                }
            ])
            ->orderBy('users.xp', 'desc')
            ->orderBy('users.level', 'desc')
            ->get()
            ->map(function ($student, $index) use ($siswa) {
                $isCurrentUser = $student->id === $siswa->id;

                return [
                    'rank' => $index + 1,
                    'id' => $student->id,
                    'name' => $student->name,
                    'username' => $student->username,
                    'avatar_url' => $student->avatar_url,
                    'xp' => $student->xp,
                    'level' => $student->level,
                    'habits_completed_count' => $student->habits_completed_count,
                    'challenges_completed_count' => $student->challenges_completed_count,
                    'total_activities' => $student->habits_completed_count + $student->challenges_completed_count,
                    'is_current_user' => $isCurrentUser, // Flag untuk siswa yang sedang login
                ];
            });

        // Dapatkan ranking siswa yang sedang login
        $currentStudentRank = $students->where('id', $siswa->id)->first()['rank'] ?? null;

        return response()->json([
            'success' => true,
            'data' => [
                'students' => $students,
                'current_student_rank' => $currentStudentRank,
                'kelas_info' => [
                    'id' => $kelas->id,
                    'nama' => $kelas->name,
                    'total_students' => $students->count()
                ],
                'current_student' => [
                    'id' => $siswa->id,
                    'name' => $siswa->name,
                    'xp' => $siswa->xp,
                    'level' => $siswa->level,
                ]
            ]
        ]);
    }

    /**
     * Get detail progress of a specific student in the same class
     */
    public function show($id)
    {
        $siswa = Auth::user();

        if ($siswa->role !== 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya siswa yang dapat mengakses.'
            ], 403);
        }

        // Dapatkan kelas siswa
        $kelas = $siswa->classAsStudent()->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak terdaftar di kelas manapun.'
            ], 404);
        }

        // Pastikan siswa yang diminta ada di kelas yang sama
        $student = $kelas->students()
            ->where('users.id', $id)
            ->where('users.role', 'siswa')
            ->select('users.*')
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan di kelas Anda.'
            ], 404);
        }

        // Hitung ranking siswa berdasarkan XP
        $ranking = $kelas->students()
            ->where('users.role', 'siswa')
            ->select('users.id')
            ->orderBy('users.xp', 'desc')
            ->get()
            ->pluck('id')
            ->search($student->id) + 1;

        // Statistik lengkap
        $stats = $this->getStudentStats($student);

        // 3 habit terakhir
        $recentHabits = $this->getRecentHabits($student);

        // 3 challenge terakhir
        $recentChallenges = $this->getRecentChallenges($student);

        $isCurrentUser = $student->id === $siswa->id;

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'username' => $student->username,
                    'avatar_url' => $student->avatar_url,
                    'email' => $isCurrentUser ? $student->email : null, // Sembunyikan email jika bukan user sendiri
                    'xp' => $student->xp,
                    'level' => $student->level,
                    'ranking' => $ranking,
                    'total_students' => $kelas->students()->where('users.role', 'siswa')->count(),
                    'is_current_user' => $isCurrentUser,
                ],
                'statistics' => $stats,
                'recent_habits' => $recentHabits,
                'recent_challenges' => $recentChallenges,
                'kelas_info' => [
                    'id' => $kelas->id,
                    'nama' => $kelas->name
                ]
            ]
        ]);
    }

    /**
     * Get current student's own detailed progress
     */
    public function myProgress()
    {
        $siswa = Auth::user();

        if ($siswa->role !== 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya siswa yang dapat mengakses.'
            ], 403);
        }

        // Redirect ke show method dengan ID sendiri
        return $this->show($siswa->id);
    }

    /**
     * Get comprehensive student statistics
     */
    private function getStudentStats(User $student)
    {
        // Total habits completed
        $habitsCompleted = $student->habitLogs()
            ->where('status', HabitStatus::COMPLETED)
            ->count();

        // Total challenges completed
        $challengesCompleted = $student->challengeParticipants()
            ->where('status', ChallengeStatus::COMPLETED)
            ->count();

        // Streak refleksi (contoh - sesuaikan dengan model reflection Anda)
        $reflectionStreak = 0; // Implementasi sesuai model reflection

        // Total XP dari habits
        $xpFromHabits = $student->habitLogs()
            ->where('status', HabitStatus::COMPLETED)
            ->join('habits', 'habit_logs.habit_id', '=', 'habits.id')
            ->sum('habits.xp_reward');

        // Total XP dari challenges
        $xpFromChallenges = $student->challengeParticipants()
            ->where('status', ChallengeStatus::COMPLETED)
            ->join('challenges', 'challenge_participants.challenge_id', '=', 'challenges.id')
            ->sum('challenges.xp_reward');

        // Habit completion rate
        $totalHabitLogs = $student->habitLogs()->count();
        $habitCompletionRate = $totalHabitLogs > 0 ? round(($habitsCompleted / $totalHabitLogs) * 100, 1) : 0;

        // Challenge completion rate
        $totalChallengeParticipants = $student->challengeParticipants()->count();
        $challengeCompletionRate = $totalChallengeParticipants > 0 ? round(($challengesCompleted / $totalChallengeParticipants) * 100, 1) : 0;

        return [
            'habits_completed' => $habitsCompleted,
            'challenges_completed' => $challengesCompleted,
            'reflection_streak' => $reflectionStreak,
            'total_activities' => $habitsCompleted + $challengesCompleted,
            'xp_from_habits' => $xpFromHabits,
            'xp_from_challenges' => $xpFromChallenges,
            'habit_completion_rate' => $habitCompletionRate,
            'challenge_completion_rate' => $challengeCompletionRate,
            'average_completion_rate' => round(($habitCompletionRate + $challengeCompletionRate) / 2, 1),
        ];
    }

    /**
     * Get 3 most recent habits with daily status
     */
    private function getRecentHabits(User $student)
    {
        return $student->habitLogs()
            ->with('habit')
            ->where('status', HabitStatus::COMPLETED)
            ->orderBy('date', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($log) use ($student) {
                // Hitung total streak untuk habit ini
                $streak = $student->habitLogs()
                    ->where('habit_id', $log->habit_id)
                    ->where('status', HabitStatus::COMPLETED)
                    ->count();

                return [
                    'id' => $log->habit_id,
                    'title' => $log->habit->title,
                    'date_completed' => $log->date->format('d M Y'),
                    'daily_status' => $log->status->value,
                    'daily_status_text' => $this->getHabitStatusText($log->status),
                    'total_streak' => $streak,
                    'xp_reward' => $log->habit->xp_reward,
                ];
            });
    }

    /**
     * Get 3 most recent completed challenges
     */
    private function getRecentChallenges(User $student)
    {
        return $student->challengeParticipants()
            ->with('challenge')
            ->where('status', ChallengeStatus::COMPLETED)
            ->orderBy('submitted_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($participant) {
                return [
                    'id' => $participant->challenge_id,
                    'title' => $participant->challenge->title,
                    'xp_reward' => $participant->challenge->xp_reward,
                    'completed_at' => $participant->submitted_at ? $participant->submitted_at->format('d M Y') : null,
                    'proof_url' => $participant->proof_url,
                ];
            });
    }

    /**
     * Helper function to get habit status text
     */
    private function getHabitStatusText($status): string
    {
        return match ($status) {
            HabitStatus::JOINED => 'Bergabung',
            HabitStatus::SUBMITTED => 'Menunggu Validasi',
            HabitStatus::COMPLETED => 'Selesai'
        };
    }

    /**
     * Get top 5 students for quick leaderboard view
     */
    public function topFive()
    {
        $siswa = Auth::user();

        if ($siswa->role !== 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya siswa yang dapat mengakses.'
            ], 403);
        }

        // Dapatkan kelas siswa
        $kelas = $siswa->classAsStudent()->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak terdaftar di kelas manapun.'
            ], 404);
        }

        // Ambil top 5 siswa
        $topStudents = $kelas->students()
            ->where('users.role', 'siswa')
            ->select('users.*')
            ->withCount([
                'habitLogs as habits_completed_count' => function ($query) {
                    $query->where('status', HabitStatus::COMPLETED);
                },
                'challengeParticipants as challenges_completed_count' => function ($query) {
                    $query->where('status', ChallengeStatus::COMPLETED);
                }
            ])
            ->orderBy('users.xp', 'desc')
            ->orderBy('users.level', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($student, $index) use ($siswa) {
                $isCurrentUser = $student->id === $siswa->id;

                return [
                    'rank' => $index + 1,
                    'id' => $student->id,
                    'name' => $student->name,
                    'avatar_url' => $student->avatar_url,
                    'xp' => $student->xp,
                    'level' => $student->level,
                    'is_current_user' => $isCurrentUser,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'top_students' => $topStudents,
                'kelas_info' => [
                    'id' => $kelas->id,
                    'nama' => $kelas->name,
                ]
            ]
        ]);
    }
}
