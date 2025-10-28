<?php

use App\Http\Controllers\Api\Guru\ChallengeController;
use App\Http\Controllers\Api\Guru\DashboardController;
use App\Http\Controllers\Api\Guru\HabitController;
use App\Http\Controllers\Api\Guru\ProfileController;
use App\Http\Controllers\Api\Guru\StudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::prefix('challenges')->group(function () {
        Route::get('/', [ChallengeController::class, 'index']);
        Route::get('/waiting-validation', [ChallengeController::class, 'waitingValidation']);
        Route::get('/{id}', [ChallengeController::class, 'show']);
        Route::post('/{participantId}/approve', [ChallengeController::class, 'approveSubmission']);
        Route::post('/{participantId}/reject', [ChallengeController::class, 'rejectSubmission']);
    });

    Route::prefix('habits')->group(function () {
        Route::get('/', [HabitController::class, 'index']);
        Route::get('/waiting-validation', [HabitController::class, 'waitingValidation']);
        Route::get('/today-submissions', [HabitController::class, 'todaySubmissions']);
        Route::get('/{id}', [HabitController::class, 'show']);
        Route::post('/{logId}/approve', [HabitController::class, 'approveSubmission']);
        Route::post('/{logId}/reject', [HabitController::class, 'rejectSubmission']);
    });

    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('/{id}', [StudentController::class, 'show']);
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
        Route::post('/password', [ProfileController::class, 'changePassword']);
    });
});
