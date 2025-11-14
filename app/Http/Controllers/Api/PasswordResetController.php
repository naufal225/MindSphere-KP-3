<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\ResetPasswordNotification;

class PasswordResetController extends Controller
{
    // Request reset password (untuk mobile)
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah user ada dan role valid
        $user = User::where('email', $request->email)
            ->whereIn('role', ['ortu', 'guru', 'siswa'])
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan atau tidak memiliki akses untuk reset password'
            ], 404);
        }

        // Generate token
        $token = Str::random(60);

        // Simpan token ke database (hapus token lama jika ada)
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // Kirim email reset password
        try {
            // Untuk mobile, kita kirim token di response juga (opsional)
            // Atau hanya kirim email saja
            $resetUrl = url('/reset-password/' . $token);
            $user->notify(new ResetPasswordNotification($token));

            return response()->json([
                'success' => true,
                'message' => 'Link reset password telah dikirim ke email Anda',
                'data' => [
                    'email' => $user->email,
                    'token' => $token // Opsional, untuk development
                ]
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Email error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email reset password. Silakan coba lagi.'
            ], 500);
        }
    }

    // Reset password dengan token (untuk mobile)
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak sesuai'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek token
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return response()->json([
                'success' => false,
                'message' => 'Token reset password tidak valid'
            ], 400);
        }

        // Cek expired token (30 menit)
        $tokenCreated = Carbon::parse($tokenData->created_at);
        if ($tokenCreated->diffInMinutes(Carbon::now()) > 30) {
            // Hapus token yang expired
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'success' => false,
                'message' => 'Token reset password telah kadaluarsa. Silakan request ulang.'
            ], 400);
        }

        // Update password user
        $user = User::where('email', $request->email)
            ->whereIn('role', ['ortu', 'guru', 'siswa'])
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token setelah digunakan
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Hapus semua token Sanctum user (untuk keamanan)
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset. Silakan login dengan password baru.'
        ], 200);
    }

    // Validasi token (untuk mobile - sebelum menampilkan form reset)
    public function validateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Token tidak valid'
            ], 400);
        }

        // Cek expired
        $tokenCreated = Carbon::parse($tokenData->created_at);
        if ($tokenCreated->diffInMinutes(Carbon::now()) > 30) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Token telah kadaluarsa'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'valid' => true,
            'message' => 'Token valid',
            'data' => [
                'email' => $tokenData->email
            ]
        ], 200);
    }

    // Check email exists (untuk pre-validation di mobile)
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Format email tidak valid'
            ], 422);
        }

        $user = User::where('email', $request->email)
            ->whereIn('role', ['ortu', 'guru', 'siswa'])
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'exists' => false,
                'message' => 'Email tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'exists' => true,
            'message' => 'Email ditemukan',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ], 200);
    }
}
