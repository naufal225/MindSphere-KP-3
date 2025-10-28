<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ParentSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentSupportController extends Controller
{
    /**
     * Get all parent supports for the authenticated student
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

        try {
            // Dapatkan semua pesan dukungan untuk siswa ini
            $parentSupports = ParentSupport::where('student_id', $siswa->id)
                ->with(['parent' => function ($query) {
                    $query->select('id', 'name', 'username', 'avatar_url');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($support) {
                    return [
                        'id' => $support->id,
                        'message' => $support->message,
                        'parent_name' => $support->parent->name,
                        'parent_username' => $support->parent->username,
                        'parent_avatar_url' => $support->parent->avatar_url,
                        'is_read' => !is_null($support->read_at),
                        'read_at' => $support->read_at?->format('d M Y H:i'),
                        'created_at' => $support->created_at->format('d M Y H:i'),
                        'time_ago' => $support->created_at->diffForHumans(),
                    ];
                });

            // Hitung jumlah pesan yang belum dibaca
            $unreadCount = ParentSupport::where('student_id', $siswa->id)
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'parent_supports' => $parentSupports,
                    'unread_count' => $unreadCount,
                    'total_count' => $parentSupports->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat pesan dukungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all unread parent supports as read
     */
    public function markAllAsRead()
    {
        $siswa = Auth::user();

        if ($siswa->role !== 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya siswa yang dapat mengakses.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Tandai semua pesan yang belum dibaca sebagai sudah dibaca
            $updatedCount = ParentSupport::where('student_id', $siswa->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "$updatedCount pesan berhasil ditandai sebagai sudah dibaca",
                'data' => [
                    'marked_as_read_count' => $updatedCount
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai pesan sebagai sudah dibaca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a specific parent support as read
     */
    public function markAsRead($id)
    {
        $siswa = Auth::user();

        if ($siswa->role !== 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya siswa yang dapat mengakses.'
            ], 403);
        }

        try {
            // Cari pesan yang dimiliki oleh siswa ini
            $parentSupport = ParentSupport::where('student_id', $siswa->id)
                ->where('id', $id)
                ->first();

            if (!$parentSupport) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesan dukungan tidak ditemukan.'
                ], 404);
            }

            // Jika sudah dibaca, kembalikan pesan
            if ($parentSupport->read_at) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan sudah ditandai sebagai sudah dibaca sebelumnya.',
                    'data' => [
                        'parent_support' => [
                            'id' => $parentSupport->id,
                            'is_read' => true,
                            'read_at' => $parentSupport->read_at->format('d M Y H:i'),
                        ]
                    ]
                ]);
            }

            // Tandai sebagai sudah dibaca
            $parentSupport->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil ditandai sebagai sudah dibaca',
                'data' => [
                    'parent_support' => [
                        'id' => $parentSupport->id,
                        'is_read' => true,
                        'read_at' => $parentSupport->read_at->format('d M Y H:i'),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai pesan sebagai sudah dibaca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread parent supports count for notifications
     */
    public function getUnreadCount()
    {
        $siswa = Auth::user();

        if ($siswa->role !== 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya siswa yang dapat mengakses.'
            ], 403);
        }

        try {
            $unreadCount = ParentSupport::where('student_id', $siswa->id)
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'unread_count' => $unreadCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat jumlah pesan belum dibaca: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get latest parent supports (for dashboard widget)
     */
    public function getLatestSupports()
    {
        $siswa = Auth::user();

        if ($siswa->role !== 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya siswa yang dapat mengakses.'
            ], 403);
        }

        try {
            $latestSupports = ParentSupport::where('student_id', $siswa->id)
                ->with(['parent' => function ($query) {
                    $query->select('id', 'name', 'username', 'avatar_url');
                }])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($support) {
                    return [
                        'id' => $support->id,
                        'message' => $support->message,
                        'parent_name' => $support->parent->name,
                        'parent_avatar_url' => $support->parent->avatar_url,
                        'is_read' => !is_null($support->read_at),
                        'created_at' => $support->created_at->format('d M Y H:i'),
                        'time_ago' => $support->created_at->diffForHumans(),
                    ];
                });

            $unreadCount = ParentSupport::where('student_id', $siswa->id)
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'latest_supports' => $latestSupports,
                    'unread_count' => $unreadCount,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat pesan terbaru: ' . $e->getMessage()
            ], 500);
        }
    }
}
