<?php

use App\Http\Controllers\Api\Siswa\ChallengeController;
use App\Http\Controllers\Api\Siswa\ChallengeSubmissionController;
use App\Http\Controllers\Api\Siswa\HabitController;
use App\Http\Controllers\Api\Siswa\DashboardController;
use App\Http\Controllers\Api\Siswa\HabitSubmissionController;
use App\Http\Controllers\Api\Siswa\ParentSupportController;
use App\Http\Controllers\Api\Siswa\ProfileController;
use App\Http\Controllers\Api\Siswa\RewardController;
use App\Http\Controllers\Api\Siswa\ReflectionController;
use App\Http\Controllers\Api\Siswa\ReflectionSubmissionController;
use App\Http\Controllers\Api\Siswa\StudentLeaderboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/habits', [HabitController::class, 'index']);

    Route::get('/challenges', [ChallengeController::class, 'index']);
    Route::post('/challenges', [ChallengeController::class, 'store']);
    Route::put('/challenges/{id}', [ChallengeController::class, 'update']);
    Route::delete('/challenges/{id}', [ChallengeController::class, 'destroy']);

    // Challenge participation routes
    Route::get('/challenges/{id}', [ChallengeSubmissionController::class, 'getChallengeDetail']);
    Route::post('/challenges/{id}/join', [ChallengeSubmissionController::class, 'joinChallenge']);
    Route::post('/challenges/{id}/submit-proof', [ChallengeSubmissionController::class, 'submitProof']);

    // Habits routes
    Route::get('/habits', [HabitController::class, 'index']);
    Route::post('/habits', [HabitController::class, 'store']);
    Route::put('/habits/{id}', [HabitController::class, 'update']);
    Route::delete('/habits/{id}', [HabitController::class, 'destroy']);

    // Habit logs routes
    Route::get('/habits/{id}', [HabitSubmissionController::class, 'getHabitDetail']);
    Route::post('/habits/{id}/join', [HabitSubmissionController::class, 'joinHabit']);
    Route::post('/habits/{id}/submit-proof', [HabitSubmissionController::class, 'submitProof']);
    Route::get('/habits/{id}/logs', [HabitSubmissionController::class, 'getHabitLogs']);
    Route::get('/habits/today', [HabitSubmissionController::class, 'getTodayHabits']);

    // Reflections routes
    Route::get('/reflections', [ReflectionController::class, 'index']);
    Route::post('/reflections', [ReflectionController::class, 'store']);
    Route::put('/reflections/{id}', [ReflectionController::class, 'update']);
    Route::delete('/reflections/{id}', [ReflectionController::class, 'destroy']);

    // Reflection submission routes
    Route::get('/reflections/today', [ReflectionSubmissionController::class, 'getTodayReflection']);
    Route::post('/reflections/mood', [ReflectionSubmissionController::class, 'submitMood']);
    Route::get('/reflections/stats', [ReflectionSubmissionController::class, 'getStats']);
    Route::get('/reflections/moods/{year}/{month}', [ReflectionSubmissionController::class, 'getMonthlyMoods']);

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/avatar', [ProfileController::class, 'updateAvatar']);
        Route::post('/password', [ProfileController::class, 'changePassword']);
    });

    Route::get('/leaderboard', [StudentLeaderboardController::class, 'index']);
    Route::get('/leaderboard/top-five', [StudentLeaderboardController::class, 'topFive']);
    Route::get('/leaderboard/my-progress', [StudentLeaderboardController::class, 'myProgress']);
    Route::get('/leaderboard/{id}', [StudentLeaderboardController::class, 'show']);

    Route::get('/parent-supports', [ParentSupportController::class, 'index']);
    Route::get('/parent-supports/unread-count', [ParentSupportController::class, 'getUnreadCount']);
    Route::get('/parent-supports/latest', [ParentSupportController::class, 'getLatestSupports']);
    Route::post('/parent-supports/mark-all-read', [ParentSupportController::class, 'markAllAsRead']);
    Route::post('/parent-supports/{id}/mark-read', [ParentSupportController::class, 'markAsRead']);

    // Rewards
    Route::get('/rewards', [RewardController::class, 'index']);
    Route::get('/rewards/{id}', [RewardController::class, 'show']);
    Route::post('/rewards/{id}/request', [RewardController::class, 'requestReward']);
    Route::get('/reward-requests', [RewardController::class, 'myRequests']);
    Route::get('/reward-requests/{id}', [RewardController::class, 'requestDetail']);
});
