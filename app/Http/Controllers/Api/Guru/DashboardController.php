<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::user();

        // Pastikan user adalah guru
        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses dashboard ini.'
            ], 403);
        }

        // Dapatkan kelas yang diajar oleh guru ini
        $kelasGuru = DB::table('school_classes')
            ->where('teacher_id', $guru->id)
            ->first();

        if (!$kelasGuru) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak memiliki kelas yang diajar.'
            ], 404);
        }

        // Statistik Utama
        $stats = $this->getStats($kelasGuru->id);

        // Leaderboard Kelas
        $leaderboard = $this->getLeaderboard($kelasGuru->id);

        // Daftar Validasi Cepat
        $validasiCepat = $this->getValidasiCepat($kelasGuru->id);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'leaderboard' => $leaderboard,
                'validasi_cepat' => $validasiCepat,
                'kelas_info' => [
                    'id' => $kelasGuru->id,
                    'nama' => $kelasGuru->name
                ]
            ]
        ]);
    }

    private function getStats($classId)
    {
        // Dapatkan semua siswa di kelas
        $siswaIds = DB::table('class_student')
            ->where('class_id', $classId)
            ->pluck('student_id');

        // Hitung habit menunggu validasi
        $habitMenungguValidasi = DB::table('habit_logs')
            ->whereIn('user_id', $siswaIds)
            ->where('status', 'submitted')
            ->count();

        // Hitung challenge menunggu validasi
        $challengeMenungguValidasi = DB::table('challenge_participants')
            ->whereIn('user_id', $siswaIds)
            ->where('status', 'submitted')
            ->count();

        // Total siswa di kelas
        $totalSiswa = $siswaIds->count();

        // Total XP kelas
        $totalXpKelas = DB::table('users')
            ->whereIn('id', $siswaIds)
            ->sum('xp');

        return [
            [
                'title' => 'Habit Menunggu Validasi',
                'value' => $habitMenungguValidasi,
                'icon' => '📝',
                'color' => 'warning'
            ],
            [
                'title' => 'Challenge Menunggu Validasi',
                'value' => $challengeMenungguValidasi,
                'icon' => '🏆',
                'color' => 'warning'
            ],
            [
                'title' => 'Total Siswa',
                'value' => $totalSiswa,
                'icon' => '👥',
                'color' => 'info'
            ],
            [
                'title' => 'Total XP Kelas',
                'value' => $totalXpKelas,
                'icon' => '⭐',
                'color' => 'success'
            ]
        ];
    }

    private function getLeaderboard($classId)
    {
        $topSiswa = DB::table('users')
            ->join('class_student', 'users.id', '=', 'class_student.student_id')
            ->where('class_student.class_id', $classId)
            ->where('users.role', 'siswa')
            ->select(
                'users.id',
                'users.name',
                'users.username',
                'users.xp',
                'users.avatar_url',
                'users.level'
            )
            ->orderBy('users.xp', 'DESC')
            ->limit(3)
            ->get()
            ->map(function ($siswa, $index) {
                $medals = ['🥇', '🥈', '🥉'];
                return [
                    'rank' => $index + 1,
                    'medal' => $medals[$index] ?? '🏅',
                    'id' => $siswa->id,
                    'nama' => $siswa->name,
                    'username' => $siswa->username,
                    'xp' => $siswa->xp,
                    'level' => $siswa->level,
                    'avatar_url' => $siswa->avatar_url
                ];
            });

        return $topSiswa;
    }

    private function getValidasiCepat($classId)
    {
        // Dapatkan semua siswa di kelas
        $siswaIds = DB::table('class_student')
            ->where('class_id', $classId)
            ->pluck('student_id');

        // Data habit yang menunggu validasi
        $habitValidasi = DB::table('habit_logs')
            ->join('habits', 'habit_logs.habit_id', '=', 'habits.id')
            ->join('users', 'habit_logs.user_id', '=', 'users.id')
            ->whereIn('habit_logs.user_id', $siswaIds)
            ->where('habit_logs.status', 'submitted')
            ->select(
                'habit_logs.id',
                'habit_logs.date',
                'habit_logs.proof_url',
                'habit_logs.note',
                'users.name as student_name',
                'users.username as student_username',
                'habits.title as habit_title',
                'habits.id as habit_id',
                DB::raw("'habit' as type")
            )
            ->get();

        // Data challenge yang menunggu validasi
        $challengeValidasi = DB::table('challenge_participants')
            ->join('challenges', 'challenge_participants.challenge_id', '=', 'challenges.id')
            ->join('users', 'challenge_participants.user_id', '=', 'users.id')
            ->whereIn('challenge_participants.user_id', $siswaIds)
            ->where('challenge_participants.status', 'submitted')
            ->select(
                'challenge_participants.id',
                'challenge_participants.submitted_at as date',
                'challenge_participants.proof_url',
                'users.name as student_name',
                'users.username as student_username',
                'challenges.title as challenge_title',
                'challenges.id as challenge_id',
                DB::raw("'challenge' as type")
            )
            ->get();

        // Gabungkan dan urutkan berdasarkan tanggal terbaru
        $allValidasi = $habitValidasi->merge($challengeValidasi)
            ->sortByDesc('date')
            ->take(10) // Ambil 10 terbaru
            ->map(function ($item) {
                $formattedDate = \Carbon\Carbon::parse($item->date)->format('d M Y');

                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'student_name' => $item->student_name,
                    'student_username' => $item->student_username,
                    'activity_title' => $item->type === 'habit' ? $item->habit_title : $item->challenge_title,
                    'activity_id' => $item->type === 'habit' ? $item->habit_id : $item->challenge_id,
                    'date' => $formattedDate,
                    'proof_url' => $item->proof_url,
                    'note' => $item->note ?? null,
                    'icon' => $item->type === 'habit' ? '🔄' : '🏆'
                ];
            });

        return $allValidasi;
    }
}
