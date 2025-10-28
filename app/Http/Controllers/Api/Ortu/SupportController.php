<?php

namespace App\Http\Controllers\Api\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ParentSupport;
use App\Models\User;

class SupportController extends Controller
{
    /**
     * Mengirim pesan dukungan ke anak
     */
    public function store(Request $request)
    {
        $parent = Auth::user();

        // Validasi bahwa user adalah orang tua
        if ($parent->role !== 'ortu') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya untuk role orang tua.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:users,id',
            'message' => 'required|string|min:5|max:500',
        ], [
            'student_id.required' => 'ID anak harus diisi',
            'student_id.exists' => 'Data anak tidak ditemukan',
            'message.required' => 'Pesan dukungan harus diisi',
            'message.min' => 'Pesan dukungan minimal 5 karakter',
            'message.max' => 'Pesan dukungan maksimal 500 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validasi bahwa anak memang milik orang tua ini
            $student = User::where('id', $request->student_id)
                ->where('parent_id', $parent->id)
                ->where('role', 'siswa')
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anak tidak ditemukan atau tidak terdaftar sebagai anak Anda'
                ], 404);
            }

            // Buat pesan dukungan
            $support = ParentSupport::create([
                'parent_id' => $parent->id,
                'student_id' => $request->student_id,
                'message' => $request->message,
                'read_at' => null, // Belum dibaca
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan dukungan berhasil dikirim ke ' . $student->name,
                'data' => [
                    'support' => [
                        'id' => $support->id,
                        'parent_name' => $parent->name,
                        'student_name' => $student->name,
                        'message' => $support->message,
                        'sent_at' => $support->created_at->format('d M Y H:i'),
                        'read_at' => $support->read_at,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan dukungan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan riwayat pesan dukungan yang dikirim
     */
    public function index()
    {
        $parent = Auth::user();

        if ($parent->role !== 'ortu') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya untuk role orang tua.'
            ], 403);
        }

        try {
            $supports = ParentSupport::with(['student' => function($query) {
                    $query->select('id', 'name', 'avatar_url');
                }])
                ->where('parent_id', $parent->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($support) {
                    return [
                        'id' => $support->id,
                        'student_id' => $support->student_id,
                        'student_name' => $support->student->name,
                        'student_avatar' => $support->student->avatar_url,
                        'message' => $support->message,
                        'sent_at' => $support->created_at->format('d M Y H:i'),
                        'read_at' => $support->read_at ? $support->read_at->format('d M Y H:i') : null,
                        'is_read' => !is_null($support->read_at),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'supports' => $supports,
                    'total_sent' => $supports->count(),
                    'total_unread_by_students' => $supports->where('is_read', false)->count(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat riwayat pesan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan detail pesan dukungan
     */
    public function show($id)
    {
        $parent = Auth::user();

        if ($parent->role !== 'ortu') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya untuk role orang tua.'
            ], 403);
        }

        try {
            $support = ParentSupport::with(['student' => function($query) {
                    $query->select('id', 'name', 'avatar_url', 'classAsStudent');
                }])
                ->where('id', $id)
                ->where('parent_id', $parent->id)
                ->first();

            if (!$support) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesan dukungan tidak ditemukan'
                ], 404);
            }

            // Dapatkan info kelas siswa
            $className = 'Belum ada kelas';
            if ($support->student->classAsStudent->isNotEmpty()) {
                $class = $support->student->classAsStudent->first();
                $className = $class->name;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'support' => [
                        'id' => $support->id,
                        'student_id' => $support->student_id,
                        'student_name' => $support->student->name,
                        'student_avatar' => $support->student->avatar_url,
                        'student_class' => $className,
                        'message' => $support->message,
                        'sent_at' => $support->created_at->format('d M Y H:i'),
                        'read_at' => $support->read_at ? $support->read_at->format('d M Y H:i') : null,
                        'is_read' => !is_null($support->read_at),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail pesan: ' . $e->getMessage()
            ], 500);
        }
    }
}
