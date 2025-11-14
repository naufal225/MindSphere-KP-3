<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Password reset routes
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
Route::post('/validate-reset-token', [PasswordResetController::class, 'validateToken']);
Route::post('/check-email', [PasswordResetController::class, 'checkEmail']);

Route::get('/user', function (Request $request) {
    return response()->json(Auth::user());
})->middleware('auth:sanctum');

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

require_once __DIR__ . '/api/siswa.php';
require_once __DIR__ . '/api/guru.php';
require_once __DIR__ . '/api/ortu.php';
