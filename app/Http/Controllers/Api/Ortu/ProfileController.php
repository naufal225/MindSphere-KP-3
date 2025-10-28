<?php

namespace App\Http\Controllers\Api\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get ortu profile data
     */
    public function show()
    {
        $ortu = Auth::user();

        if ($ortu->role !== 'ortu') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya orang tua yang dapat mengakses.'
            ], 403);
        }

        // Dapatkan data anak-anak
        $anak = $ortu->children()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'profile' => [
                    'id' => $ortu->id,
                    'name' => $ortu->name,
                    'username' => $ortu->username,
                    'email' => $ortu->email,
                    'avatar_url' => $ortu->avatar_url,
                    'role' => $ortu->role,
                    'xp' => $ortu->xp,
                    'level' => $ortu->level,
                    'created_at' => $ortu->created_at->format('d M Y'),
                ],
                'anak_info' => $anak->map(function($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'class' => $child->classAsStudent->first() ? $child->classAsStudent->first()->name : 'Belum ada kelas',
                    ];
                }),
            ]
        ]);
    }

    /**
     * Update ortu profile
     */
    public function update(Request $request)
    {
        $ortu = Auth::user();

        if ($ortu->role !== 'ortu') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya orang tua yang dapat mengakses.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $ortu->id,
            'email' => 'sometimes|required|email|unique:users,email,' . $ortu->id,
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
                $ortu->update($updateData);
            }

            // Refresh user data
            $ortu->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'profile' => [
                        'id' => $ortu->id,
                        'name' => $ortu->name,
                        'username' => $ortu->username,
                        'email' => $ortu->email,
                        'avatar_url' => $ortu->avatar_url,
                        'role' => $ortu->role,
                        'xp' => $ortu->xp,
                        'level' => $ortu->level,
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
     * Update avatar ortu
     */
    public function updateAvatar(Request $request)
    {
        $ortu = Auth::user();

        if ($ortu->role !== 'ortu') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya orang tua yang dapat mengakses.'
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
            if ($ortu->avatar_url && Storage::exists($ortu->avatar_url)) {
                Storage::delete($ortu->avatar_url);
            }

            // Simpan avatar baru
            $avatarPath = $request->file('avatar')->store('avatars', 'public');

            // Update avatar URL
            $ortu->update([
                'avatar_url' => $avatarPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar berhasil diperbarui',
                'data' => [
                    'avatar_url' => Storage::url($avatarPath),
                    'profile' => [
                        'id' => $ortu->id,
                        'name' => $ortu->name,
                        'username' => $ortu->username,
                        'email' => $ortu->email,
                        'avatar_url' => Storage::url($avatarPath),
                        'role' => $ortu->role,
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
        $ortu = Auth::user();

        if ($ortu->role !== 'ortu') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya orang tua yang dapat mengakses.'
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
        if (!Hash::check($request->current_password, $ortu->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini tidak sesuai'
            ], 422);
        }

        try {
            $ortu->update([
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
