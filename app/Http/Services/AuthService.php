<?php

namespace App\Http\Services;

use App\Exceptions\NotAdminException;
use App\Exceptions\WrongPasswordException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthService
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = (bool) $request->filled('remember'); // cek apakah checkbox remember diisi

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            if ($user->role !== 'admin') {
                Auth::logout();
                throw new NotAdminException();
            }

            return $user; // langsung return user
        }

        throw new WrongPasswordException();
    }
}
