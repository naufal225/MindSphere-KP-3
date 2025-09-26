<?php

namespace App\Http\Controllers;

use App\Exceptions\NotAdminException;
use App\Exceptions\WrongPasswordException;
use App\Http\Requests\LoginRequest;
use App\Http\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function index()
    {
        return view('auth.index');
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = $this->authService->login($request);

            // Jika berhasil login sebagai admin
            return redirect()->intended('/admin/dashboard');

        } catch (WrongPasswordException $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        } catch (NotAdminException $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
