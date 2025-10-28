<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use App\Enums\HabitStatus;
use App\Enums\ChallengeStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Get all students in guru's class ordered by XP
     */
    public function index(Request $request)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        // Dapatkan kelas yang diajar oleh guru
        $kelas = SchoolClass::where('teacher_id', $guru->id)->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak memiliki kelas yang diajar.'
            ], 404);
        }

        // Gunakan select untuk menghindari ambiguity
        $students = $kelas->students()
            ->where('users.role', 'siswa')
            ->select('users.*') // Spesifik tabel users
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
            ->map(function ($student, $index) {
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
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'students' => $students,
                'kelas_info' => [
                    'id' => $kelas->id,
                    'nama' => $kelas->name,
                    'total_students' => $students->count()
                ]
            ]
        ]);
    }

    /**
     * Get student detail with comprehensive statistics
     */
    public function show($id)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        // Dapatkan kelas yang diajar oleh guru
        $kelas = SchoolClass::where('teacher_id', $guru->id)->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak memiliki kelas yang diajar.'
            ], 404);
        }

        // Pastikan siswa tersebut ada di kelas guru
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

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'username' => $student->username,
                    'avatar_url' => $student->avatar_url,
                    'email' => $student->email,
                    'xp' => $student->xp,
                    'level' => $student->level,
                    'ranking' => $ranking,
                    'total_students' => $kelas->students()->where('users.role', 'siswa')->count(),
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
}
