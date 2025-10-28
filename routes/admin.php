<?php

use App\Http\Controllers\Admin\BadgeController;
use App\Http\Controllers\Admin\ChallengeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HabitController;
use App\Http\Controllers\Admin\ProfileController;
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
    Route::get('/categories/{category}/badges', [CategoryController::class, 'showBadges'])->name('categories.badges');
    Route::get('/categories/{category}/reflections', [CategoryController::class, 'showReflections'])->name('categories.reflections');

    Route::resource('challenges', ChallengeController::class);

    Route::resource('habits', HabitController::class);

    Route::resource('badges', BadgeController::class);

    Route::prefix('user-progress')->name('user-progress.')->group(function () {
        Route::get('/', [UserProgressController::class, 'index'])->name('index');
        Route::get('/export', [UserProgressController::class, 'export'])->name('export');
        Route::get('/chart-data', [UserProgressController::class, 'getChartData'])->name('chart-data');
        Route::get('/filter-options', [UserProgressController::class, 'getFilterOptions'])->name('filter-options');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->name('update-profile');
        Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::post('/update-avatar', [ProfileController::class, 'updateAvatar'])->name('update-avatar');
        Route::delete('/delete-avatar', [ProfileController::class, 'deleteAvatar'])->name('delete-avatar');
    });
});
