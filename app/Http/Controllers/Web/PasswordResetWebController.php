<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Notifications\ResetPasswordNotification;

class PasswordResetWebController extends Controller
{
    // Show form untuk input email (forgot password)
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Process send reset link email
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        // Cek apakah user ada dan role valid
        $user = User::where('email', $request->email)
        ->whereIn('role', ['ortu', 'guru', 'siswa', 'admin'])
        ->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan atau tidak memiliki akses untuk reset password.');
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
            $user->notify(new ResetPasswordNotification($token));

            return redirect()->route('password.sent');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email reset password. Silakan coba lagi.');
        }
    }

    // Show reset password form (setelah klik link di email)
    public function showResetForm(Request $request, $token)
    {
        // Cek token
        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$tokenData) {
            return view('auth.reset-password', [
                'error' => 'Token reset password tidak valid atau sudah digunakan.',
                'token' => $token,
                'valid' => false
            ]);
        }

        // Cek expired token (30 menit)
        $tokenCreated = Carbon::parse($tokenData->created_at);
        if ($tokenCreated->diffInMinutes(Carbon::now()) > 30) {
            // Hapus token yang expired
            DB::table('password_reset_tokens')
                ->where('token', $token)
                ->delete();

            return view('auth.reset-password', [
                'error' => 'Token reset password telah kadaluarsa. Silakan request reset password lagi.',
                'token' => $token,
                'valid' => false
            ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $tokenData->email, // Email dari token, tidak perlu diinput lagi
            'valid' => true
        ]);
    }

    // Process reset password
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return view('auth.reset-password', [
                'error' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
                'token' => $request->token,
                'email' => $request->email,
                'valid' => true
            ]);
        }

        // Validasi: email harus match dengan token
        $tokenData = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return view('auth.reset-password', [
                'error' => 'Token reset password tidak valid.',
                'token' => $request->token,
                'email' => $request->email,
                'valid' => false
            ]);
        }

        // Cek expired token
        $tokenCreated = Carbon::parse($tokenData->created_at);
        if ($tokenCreated->diffInMinutes(Carbon::now()) > 30) {
            DB::table('password_reset_tokens')
                ->where('token', $request->token)
                ->delete();

            return view('auth.reset-password', [
                'error' => 'Token reset password telah kadaluarsa.',
                'token' => $request->token,
                'email' => $tokenData->email,
                'valid' => false
            ]);
        }

        // Update password user
        $user = User::where('email', $tokenData->email)
            ->whereIn('role', ['ortu', 'guru', 'siswa', 'admin'])
            ->first();

        if (!$user) {
            return view('auth.reset-password', [
                'error' => 'User tidak ditemukan.',
                'token' => $request->token,
                'email' => $tokenData->email,
                'valid' => false
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token setelah digunakan
        DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->delete();

        // Hapus semua token Sanctum user
        $user->tokens()->delete();

        return view('auth.reset-success');
    }

    // Show confirmation page setelah email dikirim
    public function showResetSent()
    {
        return view('auth.reset-sent');
    }
}
