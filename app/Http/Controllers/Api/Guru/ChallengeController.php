<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeParticipant;
use App\Models\SchoolClass;
use App\Models\User;
use App\Enums\ChallengeStatus;
use App\Http\Services\LevelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengeController extends Controller
{
    /**
     * Get all challenges for guru's class
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

        $challenges = Challenge::with(['category', 'createdBy'])
            ->whereHas('participants', function($query) use ($kelas) {
                $query->whereIn('user_id', $kelas->students()->pluck('users.id'));
            })
            ->orWhere('created_by', $guru->id)
            ->withCount(['participants as total_participants'])
            ->withCount(['participants as submitted_count' => function($query) {
                $query->where('status', ChallengeStatus::SUBMITTED);
            }])
            ->withCount(['participants as completed_count' => function($query) {
                $query->where('status', ChallengeStatus::COMPLETED);
            }])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($challenge) use ($kelas) {
                return [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'description' => $challenge->description,
                    'type' => $challenge->type,
                    'category' => $challenge->category->name,
                    'xp_reward' => $challenge->xp_reward,
                    'start_date' => $challenge->start_date->format('d M Y'),
                    'end_date' => $challenge->end_date->format('d M Y'),
                    'created_by' => $challenge->createdBy->name,
                    'total_participants' => $challenge->total_participants,
                    'submitted_count' => $challenge->submitted_count,
                    'completed_count' => $challenge->completed_count,
                    'progress_percentage' => $challenge->total_participants > 0
                        ? round(($challenge->completed_count / $challenge->total_participants) * 100, 1)
                        : 0,
                    'is_expired' => $challenge->end_date->isPast(),
                    'days_remaining' => max(0, now()->diffInDays($challenge->end_date, false)),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'challenges' => $challenges,
                'kelas_info' => [
                    'id' => $kelas->id,
                    'nama' => $kelas->name
                ]
            ]
        ]);
    }

    /**
     * Get challenge detail with student progress
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

        $challenge = Challenge::with(['category', 'createdBy'])
            ->with(['participants' => function($query) use ($kelas) {
                $query->whereIn('user_id', $kelas->students()->pluck('users.id'))
                      ->with('user');
            }])
            ->find($id);

        if (!$challenge) {
            return response()->json([
                'success' => false,
                'message' => 'Challenge tidak ditemukan.'
            ], 404);
        }

        // Hitung statistik
        $totalStudents = $kelas->students()->count();
        $participantsCount = $challenge->participants->count();
        $submittedCount = $challenge->participants->where('status', ChallengeStatus::SUBMITTED)->count();
        $completedCount = $challenge->participants->where('status', ChallengeStatus::COMPLETED)->count();
        $joinedCount = $challenge->participants->where('status', ChallengeStatus::JOINED)->count();

        // Format data peserta
        $participants = $challenge->participants->map(function($participant) {
            return [
                'id' => $participant->id,
                'student_id' => $participant->user_id,
                'student_name' => $participant->user->name,
                'student_username' => $participant->user->username,
                'student_avatar' => $participant->user->avatar_url,
                'status' => $participant->status,
                'status_text' => $this->getStatusText($participant->status),
                'proof_url' => $participant->proof_url,
                'submitted_at' => $participant->submitted_at?->format('d M Y H:i'),
                'created_at' => $participant->created_at->format('d M Y'),
                'can_validate' => $participant->status === ChallengeStatus::SUBMITTED,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'challenge' => [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'description' => $challenge->description,
                    'type' => $challenge->type,
                    'category' => $challenge->category->name,
                    'xp_reward' => $challenge->xp_reward,
                    'start_date' => $challenge->start_date->format('d M Y'),
                    'end_date' => $challenge->end_date->format('d M Y'),
                    'created_by' => $challenge->createdBy->name,
                    'is_expired' => $challenge->end_date->isPast(),
                    'days_remaining' => max(0, now()->diffInDays($challenge->end_date, false)),
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
                ],
                'participants' => $participants,
                'kelas_info' => [
                    'id' => $kelas->id,
                    'nama' => $kelas->name
                ]
            ]
        ]);
    }

    /**
     * Approve challenge submission
     */
    public function approveSubmission(Request $request, $participantId)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        $participant = ChallengeParticipant::with(['challenge', 'user'])
            ->where('id', $participantId)
            ->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Partisipan challenge tidak ditemukan.'
            ], 404);
        }

        // Validasi bahwa siswa tersebut adalah siswa dari guru yang bersangkutan
        $kelas = SchoolClass::where('teacher_id', $guru->id)
            ->whereHas('students', function($query) use ($participant) {
                $query->where('users.id', $participant->user_id);
            })
            ->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan di kelas Anda.'
            ], 403);
        }

        if ($participant->status !== ChallengeStatus::SUBMITTED) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya submission yang menunggu validasi yang dapat disetujui.'
            ], 400);
        }

        DB::transaction(function() use ($participant) {
            // Update status participant
            $participant->update([
                'status' => ChallengeStatus::COMPLETED
            ]);

            // Tambahkan XP ke user
            $user = $participant->user;
            $xpReward = $participant->challenge->xp_reward;

            $user->update([
                'xp' => $user->xp + $xpReward
            ]);

            // Update level user menggunakan LevelService
            LevelService::updateUserLevel($user);
        });

        return response()->json([
            'success' => true,
            'message' => 'Bukti challenge berhasil disetujui. XP telah diberikan kepada siswa.',
            'data' => [
                'participant_id' => $participant->id,
                'status' => ChallengeStatus::COMPLETED,
                'xp_awarded' => $participant->challenge->xp_reward,
                'student_new_xp' => $participant->user->fresh()->xp,
                'student_new_level' => $participant->user->fresh()->level
            ]
        ]);
    }

    /**
     * Reject challenge submission
     */
    public function rejectSubmission(Request $request, $participantId)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        $participant = ChallengeParticipant::with(['challenge', 'user'])
            ->where('id', $participantId)
            ->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Partisipan challenge tidak ditemukan.'
            ], 404);
        }

        // Validasi bahwa siswa tersebut adalah siswa dari guru yang bersangkutan
        $kelas = SchoolClass::where('teacher_id', $guru->id)
            ->whereHas('students', function($query) use ($participant) {
                $query->where('users.id', $participant->user_id);
            })
            ->first();

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan di kelas Anda.'
            ], 403);
        }

        if ($participant->status !== ChallengeStatus::SUBMITTED) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya submission yang menunggu validasi yang dapat ditolak.'
            ], 400);
        }

        $participant->update([
            'status' => ChallengeStatus::JOINED,
            'proof_url' => null,
            'submitted_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti challenge ditolak. Siswa dapat mengirim bukti kembali.',
            'data' => [
                'participant_id' => $participant->id,
                'status' => ChallengeStatus::JOINED
            ]
        ]);
    }

    /**
     * Get challenges waiting for validation
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

        $waitingValidation = ChallengeParticipant::with(['challenge', 'user'])
            ->whereIn('user_id', $kelas->students()->pluck('users.id'))
            ->where('status', ChallengeStatus::SUBMITTED)
            ->orderBy('submitted_at', 'desc')
            ->get()
            ->map(function($participant) {
                return [
                    'id' => $participant->id,
                    'challenge_id' => $participant->challenge_id,
                    'challenge_title' => $participant->challenge->title,
                    'student_id' => $participant->user_id,
                    'student_name' => $participant->user->name,
                    'student_username' => $participant->user->username,
                    'proof_url' => $participant->proof_url,
                    'submitted_at' => $participant->submitted_at->format('d M Y H:i'),
                    'days_ago' => $participant->submitted_at->diffForHumans(),
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
     * Helper function to get status text
     */
    private function getStatusText(ChallengeStatus $status): string
    {
        return match($status) {
            ChallengeStatus::JOINED => 'Bergabung',
            ChallengeStatus::SUBMITTED => 'Menunggu Validasi',
            ChallengeStatus::COMPLETED => 'Selesai'
        };
    }
}
