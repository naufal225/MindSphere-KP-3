<?php

use App\Http\Controllers\Api\Siswa\ChallengeController;
use App\Http\Controllers\Api\Siswa\HabitController;
use App\Http\Controllers\Api\Siswa\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/habits', [HabitController::class, 'index']);
    Route::get('/challenges', [ChallengeController::class, 'index']);

});
