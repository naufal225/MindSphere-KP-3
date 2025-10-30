<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\SchoolClass;
use App\Models\User;
use App\Enums\HabitStatus;
use App\Http\Services\LevelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HabitController extends Controller
{
    /**
     * Get all habits for guru's class
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

        $habits = Habit::with(['category', 'createdBy'])
            ->whereHas('logs', function($query) use ($kelas) {
                $query->whereIn('user_id', $kelas->students()->pluck('users.id'));
            })
            ->orWhere('created_by', $guru->id)
            ->orWhere('assigned_by', $guru->id)
            ->withCount(['logs as total_participants' => function($query) use ($kelas) {
                $query->whereIn('user_id', $kelas->students()->pluck('users.id'));
            }])
            ->withCount(['logs as submitted_count' => function($query) use ($kelas) {
                $query->whereIn('user_id', $kelas->students()->pluck('users.id'))
                      ->where('status', HabitStatus::SUBMITTED);
            }])
            ->withCount(['logs as completed_count' => function($query) use ($kelas) {
                $query->whereIn('user_id', $kelas->students()->pluck('users.id'))
                      ->where('status', HabitStatus::COMPLETED);
            }])
            ->withCount(['logs as today_submitted_count' => function($query) use ($kelas) {
                $query->whereIn('user_id', $kelas->students()->pluck('users.id'))
                      ->where('status', HabitStatus::SUBMITTED)
                      ->whereDate('date', today());
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($habit) {
                return [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'description' => $habit->description,
                    'type' => $habit->type,
                    'period' => $habit->period,
                    'category' => $habit->category->name,
                    'xp_reward' => $habit->xp_reward,
                    'created_by' => $habit->createdBy->name,
                    'assigned_by' => $habit->assignedBy?->name,
                    'total_participants' => $habit->total_participants,
                    'submitted_count' => $habit->submitted_count,
                    'completed_count' => $habit->completed_count,
                    'today_submitted_count' => $habit->today_submitted_count,
                    'progress_percentage' => $habit->total_participants > 0
                        ? round(($habit->completed_count / $habit->total_participants) * 100, 1)
                        : 0,
                    'is_active' => true, // Habits are always active unless deleted
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'habits' => $habits,
                'kelas_info' => [
                    'id' => $kelas->id,
                    'nama' => $kelas->name
                ]
            ]
        ]);
    }

    /**
     * Get habit detail with student progress
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

        $kelas = SchoolClass::where('teacher_id', $guru->id)->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak memiliki kelas yang diajar.'
            ], 404);
        }

        $habit = Habit::with(['category', 'createdBy', 'assignedBy'])
            ->with(['logs' => function($query) use ($kelas) {
                $query->whereIn('user_id', $kelas->students()->pluck('users.id'))
                      ->with('user')
                      ->orderBy('date', 'desc')
                      ->orderBy('created_at', 'desc');
            }])
            ->find($id);

        if (!$habit) {
            return response()->json([
                'success' => false,
                'message' => 'Habit tidak ditemukan.'
            ], 404);
        }

        // Hitung statistik
        $totalStudents = $kelas->students()->count();
        $participantsCount = $habit->logs->groupBy('user_id')->count();
        $submittedCount = $habit->logs->where('status', HabitStatus::SUBMITTED)->count();
        $completedCount = $habit->logs->where('status', HabitStatus::COMPLETED)->count();
        $joinedCount = $habit->logs->where('status', HabitStatus::JOINED)->count();

        // Get today's submissions
        $todaySubmissions = $habit->logs
            ->where('status', HabitStatus::SUBMITTED)
            ->where('date', today())
            ->values();

        // Format data logs (group by student for better overview)
        $studentProgress = [];
        $studentLogs = $habit->logs->groupBy('user_id');

        foreach ($studentLogs as $userId => $logs) {
            $student = $logs->first()->user;
            $completedLogs = $logs->where('status', HabitStatus::COMPLETED)->count();
            $submittedLogs = $logs->where('status', HabitStatus::SUBMITTED)->count();
            $totalLogs = $logs->count();

            $studentProgress[] = [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'student_username' => $student->username,
                'student_avatar' => $student->avatar_url,
                'total_logs' => $totalLogs,
                'completed_logs' => $completedLogs,
                'submitted_logs' => $submittedLogs,
                'completion_rate' => $totalLogs > 0 ? round(($completedLogs / $totalLogs) * 100, 1) : 0,
                'latest_status' => $logs->first()->status,
                'latest_status_text' => $this->getStatusText($logs->first()->status),
                'can_validate_today' => $logs->where('date', today())
                    ->where('status', HabitStatus::SUBMITTED)
                    ->count() > 0,
                'today_log_id' => $logs->where('date', today())
                    ->where('status', HabitStatus::SUBMITTED)
                    ->first()?->id,
            ];
        }

        // Recent submissions (last 7 days)
        $recentSubmissions = $habit->logs
            ->where('status', HabitStatus::SUBMITTED)
            ->where('date', '>=', now()->subDays(7))
            ->sortByDesc('date')
            ->values()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'student_name' => $log->user->name,
                    'student_username' => $log->user->username,
                    'date' => $log->date->format('d M Y'),
                    'proof_url' => $log->proof_url,
                    'note' => $log->note,
                    'submitted_at' => $log->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'habit' => [
                    'id' => $habit->id,
                    'title' => $habit->title,
                    'description' => $habit->description,
                    'type' => $habit->type,
                    'period' => $habit->period,
                    'category' => $habit->category->name,
                    'xp_reward' => $habit->xp_reward,
                    'created_by' => $habit->createdBy->name,
                    'assigned_by' => $habit->assignedBy?->name,
                    'is_assigned' => $habit->type === 'assigned',
                ],
                'statistics' => [
                    'total_students' => $totalStudents,
                    'participants_count' => $participantsCount,
                    'submitted_count' => $submittedCount,
                    'completed_count' => $completedCount,
                    'joined_count' => $joinedCount,
                    'participation_rate' => $totalStudents > 0
                        ? round(($participantsCount / $totalStudents) * 100, 1)
                        : 0,
                    'completion_rate' => $participantsCount > 0
                        ? round(($completedCount / $participantsCount) * 100, 1)
                        : 0,
                    'today_submissions' => $todaySubmissions->count(),
                ],
                'student_progress' => $studentProgress,
                'recent_submissions' => $recentSubmissions,
                'kelas_info' => [
                    'id' => $kelas->id,
                    'nama' => $kelas->name
                ]
            ]
        ]);
    }

    /**
     * Approve habit submission
     */
    public function approveSubmission(Request $request, $logId)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        $habitLog = HabitLog::with(['habit', 'user'])
            ->where('id', $logId)
            ->first();

        if (!$habitLog) {
            return response()->json([
                'success' => false,
                'message' => 'Log habit tidak ditemukan.'
            ], 404);
        }

        // Validasi bahwa siswa tersebut adalah siswa dari guru yang bersangkutan
        $kelas = SchoolClass::where('teacher_id', $guru->id)
            ->whereHas('students', function($query) use ($habitLog) {
                $query->where('users.id', $habitLog->user_id);
            })
            ->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan di kelas Anda.'
            ], 403);
        }

        if ($habitLog->status !== HabitStatus::SUBMITTED) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya submission yang menunggu validasi yang dapat disetujui.'
            ], 400);
        }

        DB::transaction(function() use ($habitLog) {
            // Update status habit log
            $habitLog->update([
                'status' => HabitStatus::COMPLETED
            ]);

            // Tambahkan XP ke user
            $user = $habitLog->user;
            $xpReward = $habitLog->habit->xp_reward;

            $user->update([
                'xp' => $user->xp + $xpReward
            ]);

            // Update level user menggunakan LevelService
            LevelService::updateUserLevel($user);
        });

        return response()->json([
            'success' => true,
            'message' => 'Bukti habit berhasil disetujui. XP telah diberikan kepada siswa.',
            'data' => [
                'log_id' => $habitLog->id,
                'status' => HabitStatus::COMPLETED,
                'xp_awarded' => $habitLog->habit->xp_reward,
                'student_new_xp' => $habitLog->user->fresh()->xp,
                'student_new_level' => $habitLog->user->fresh()->level,
                'date' => $habitLog->date->format('d M Y')
            ]
        ]);
    }

    /**
     * Reject habit submission
     */
    public function rejectSubmission(Request $request, $logId)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        $habitLog = HabitLog::with(['habit', 'user'])
            ->where('id', $logId)
            ->first();

        if (!$habitLog) {
            return response()->json([
                'success' => false,
                'message' => 'Log habit tidak ditemukan.'
            ], 404);
        }

        // Validasi bahwa siswa tersebut adalah siswa dari guru yang bersangkutan
        $kelas = SchoolClass::where('teacher_id', $guru->id)
            ->whereHas('students', function($query) use ($habitLog) {
                $query->where('users.id', $habitLog->user_id);
            })
            ->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan di kelas Anda.'
            ], 403);
        }

        if ($habitLog->status !== HabitStatus::SUBMITTED) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya submission yang menunggu validasi yang dapat ditolak.'
            ], 400);
        }

        $habitLog->update([
            'status' => HabitStatus::JOINED,
            'proof_url' => null,
            'note' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti habit ditolak. Siswa dapat mengirim bukti kembali.',
            'data' => [
                'log_id' => $habitLog->id,
                'status' => HabitStatus::JOINED,
                'date' => $habitLog->date->format('d M Y')
            ]
        ]);
    }

    /**
     * Get habits waiting for validation
     */
    public function waitingValidation(Request $request)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        $kelas = SchoolClass::where('teacher_id', $guru->id)->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak memiliki kelas yang diajar.'
            ], 404);
        }

        $waitingValidation = HabitLog::with(['habit', 'user'])
            ->whereIn('user_id', $kelas->students()->pluck('users.id'))
            ->where('status', HabitStatus::SUBMITTED)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'habit_id' => $log->habit_id,
                    'habit_title' => $log->habit->title,
                    'student_id' => $log->user_id,
                    'student_name' => $log->user->name,
                    'student_username' => $log->user->username,
                    'proof_url' => $log->proof_url,
                    'note' => $log->note,
                    'date' => $log->date->format('d M Y'),
                    'submitted_at' => $log->created_at->format('d M Y H:i'),
                    'days_ago' => $log->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'waiting_validation' => $waitingValidation,
                'count' => $waitingValidation->count(),
                'kelas_info' => [
                    'id' => $kelas->id,
                    'nama' => $kelas->name
                ]
            ]
        ]);
    }

    /**
     * Get today's submissions for quick validation
     */
    public function todaySubmissions(Request $request)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        $kelas = SchoolClass::where('teacher_id', $guru->id)->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak memiliki kelas yang diajar.'
            ], 404);
        }

        $todaySubmissions = HabitLog::with(['habit', 'user'])
            ->whereIn('user_id', $kelas->students()->pluck('users.id'))
            ->where('status', HabitStatus::SUBMITTED)
            ->whereDate('date', today())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($log) {
                return [
                    'id' => $log->id,
                    'habit_id' => $log->habit_id,
                    'habit_title' => $log->habit->title,
                    'student_id' => $log->user_id,
                    'student_name' => $log->user->name,
                    'student_username' => $log->user->username,
                    'proof_url' => $log->proof_url,
                    'note' => $log->note,
                    'date' => $log->date->format('d M Y'),
                    'submitted_at' => $log->created_at->format('H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'today_submissions' => $todaySubmissions,
                'count' => $todaySubmissions->count(),
                'kelas_info' => [
                    'id' => $kelas->id,
                    'nama' => $kelas->name
                ]
            ]
        ]);
    }

    /**
     * Helper function to get status text
     */
    private function getStatusText(HabitStatus $status): string
    {
        return match($status) {
            HabitStatus::JOINED => 'Bergabung',
            HabitStatus::SUBMITTED => 'Menunggu Validasi',
            HabitStatus::COMPLETED => 'Selesai'
        };
    }
}
