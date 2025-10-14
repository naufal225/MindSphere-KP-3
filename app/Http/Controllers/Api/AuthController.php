<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek apakah user ada dan role-nya valid
        $user = User::where('email', $request->email)
            ->whereIn('role', ['ortu', 'guru', 'siswa'])
            ->first();

        if (!$user || !Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Login gagal, email atau password salah',
            ], 401);
        }

        // Hapus token lama (opsional, untuk keamanan)
        $user->tokens()->delete();

        // Buat token Sanctum
        $token = $user->createToken('mobile-token')->plainTextToken;

        // Siapkan data user tanpa password
        $userData = $user->toArray();
        unset($userData['password']);

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $userData,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }
}
