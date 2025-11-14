<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\PasswordResetWebController;

// Reset Password Routes for Web View
Route::get('/forgot-password', [PasswordResetWebController::class, 'showForgotPasswordForm'])
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetWebController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetWebController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetWebController::class, 'reset'])
    ->name('password.update');

Route::get('/reset-password-sent', [PasswordResetWebController::class, 'showResetSent'])
    ->name('password.sent');

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/admin.php';

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('login');
});

