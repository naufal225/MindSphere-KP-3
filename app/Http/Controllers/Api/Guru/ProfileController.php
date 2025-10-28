<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get guru profile data
     */
    public function show()
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        // Dapatkan kelas yang diajar oleh guru
        $kelas = $guru->classesAsTeacher()->first();

        return response()->json([
            'success' => true,
            'data' => [
                'profile' => [
                    'id' => $guru->id,
                    'name' => $guru->name,
                    'username' => $guru->username,
                    'email' => $guru->email,
                    'avatar_url' => $guru->avatar_url,
                    'role' => $guru->role,
                    'xp' => $guru->xp,
                    'level' => $guru->level,
                    'created_at' => $guru->created_at->format('d M Y'),
                ],
                'kelas_info' => $kelas ? [
                    'id' => $kelas->id,
                    'nama' => $kelas->name,
                ] : null,
            ]
        ]);
    }

    /**
     * Update guru profile
     */
    public function update(Request $request)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $guru->id,
            'email' => 'sometimes|required|email|unique:users,email,' . $guru->id,
        ], [
            'name.required' => 'Nama harus diisi',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [];

            if ($request->has('name')) {
                $updateData['name'] = $request->name;
            }

            if ($request->has('username')) {
                $updateData['username'] = $request->username;
            }

            if ($request->has('email')) {
                $updateData['email'] = $request->email;
            }

            if (!empty($updateData)) {
                $guru->update($updateData);
            }

            // Refresh user data
            $guru->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'profile' => [
                        'id' => $guru->id,
                        'name' => $guru->name,
                        'username' => $guru->username,
                        'email' => $guru->email,
                        'avatar_url' => $guru->avatar_url,
                        'role' => $guru->role,
                        'xp' => $guru->xp,
                        'level' => $guru->level,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update avatar guru
     */
    public function updateAvatar(Request $request)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'avatar.required' => 'Avatar harus diisi',
            'avatar.image' => 'File harus berupa gambar',
            'avatar.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'avatar.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Hapus avatar lama jika ada
            if ($guru->avatar_url && Storage::exists($guru->avatar_url)) {
                Storage::delete($guru->avatar_url);
            }

            // Simpan avatar baru
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            // Update avatar URL
            $guru->update([
                'avatar_url' => $avatarPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar berhasil diperbarui',
                'data' => [
                    'avatar_url' => Storage::url($avatarPath),
                    'profile' => [
                        'id' => $guru->id,
                        'name' => $guru->name,
                        'username' => $guru->username,
                        'email' => $guru->email,
                        'avatar_url' => Storage::url($avatarPath),
                        'role' => $guru->role,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui avatar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $guru = Auth::user();

        if ($guru->role !== 'guru') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya guru yang dapat mengakses.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 6 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check current password
        if (!Hash::check($request->current_password, $guru->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai'
            ], 422);
        }

        try {
            $guru->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah password: ' . $e->getMessage()
            ], 500);
        }
    }
}
