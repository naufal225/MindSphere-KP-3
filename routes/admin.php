<?php

use App\Http\Controllers\Admin\ChallengeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HabitController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\RewardRequestController;
use App\Http\Controllers\Admin\UserProgressController;
use App\Models\ForumComment;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('users', UserController::class);

    Route::resource('school_classes', SchoolClassController::class);

    Route::resource('categories', CategoryController::class);

    // Routes untuk menampilkan detail konten berdasarkan kategori
    Route::get('/categories/{category}/habits', [CategoryController::class, 'showHabits'])->name('categories.habits');
    Route::get('/categories/{category}/challenges', [CategoryController::class, 'showChallenges'])->name('categories.challenges');
    Route::get('/categories/{category}/reflections', [CategoryController::class, 'showReflections'])->name('categories.reflections');

    Route::resource('challenges', ChallengeController::class);

    Route::resource('habits', HabitController::class);

    Route::prefix('user-progress')->name('user-progress.')->group(function () {
        Route::get('/', [UserProgressController::class, 'index'])->name('index');
        Route::get('/export', [UserProgressController::class, 'export'])->name('export');
        Route::get('/chart-data', [UserProgressController::class, 'getChartData'])->name('chart-data');
        Route::get('/filter-options', [UserProgressController::class, 'getFilterOptions'])->name('filter-options');
    });

    // Resource untuk rewards
    Route::resource('rewards', RewardController::class)->except(['show']);
    // Route untuk Reward Requests
    Route::prefix('rewards')->group(function () {

        // Custom routes untuk rewards
        Route::prefix('rewards/{reward}')->group(function () {
            Route::post('toggle-status', [RewardController::class, 'toggleStatus'])
                ->name('rewards.toggle-status');

            Route::post('update-stock', [RewardController::class, 'updateStock'])
                ->name('rewards.update-stock');

            Route::get('export', [RewardController::class, 'export'])
                ->name('rewards.export');
        });

        // Route untuk Reward Requests
        Route::prefix('requests')->group(function () {
            Route::get('/', [RewardRequestController::class, 'index'])->name('requests.index');
            Route::get('/{rewardRequest}', [RewardRequestController::class, 'show'])->name('requests.show');
            Route::post('/{rewardRequest}/approve', [RewardRequestController::class, 'approve'])->name('requests.approve');
            Route::post('/{rewardRequest}/reject', [RewardRequestController::class, 'reject'])->name('requests.reject');
            Route::post('/{rewardRequest}/complete', [RewardRequestController::class, 'complete'])->name('requests.complete');
            Route::post('/{rewardRequest}/cancel', [RewardRequestController::class, 'cancel'])->name('requests.cancel');
            Route::get('/export', [RewardRequestController::class, 'export'])->name('requests.export');
            Route::get('/statistics', [RewardRequestController::class, 'statistics'])->name('requests.statistics');
        });
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->name('update-profile');
        Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::post('/update-avatar', [ProfileController::class, 'updateAvatar'])->name('update-avatar');
        Route::delete('/delete-avatar', [ProfileController::class, 'deleteAvatar'])->name('delete-avatar');
    });
});
