<?php

use App\Http\Controllers\Admin\BadgeController;
use App\Http\Controllers\Admin\ChallengeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ForumCommentController;
use App\Http\Controllers\Admin\ForumController;
use App\Http\Controllers\Admin\HabitController;
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

    // Forum Management Routes
    Route::resource('forum', ForumController::class)->only(['index', 'show'])->parameters([
        'forum' => 'post'
    ]);
    Route::post('forum/{post}/lock', [ForumController::class, 'lock'])->name('forum.lock');
    Route::post('forum/{post}/unlock', [ForumController::class, 'unlock'])->name('forum.unlock');
    Route::post('forum/{post}/toggle-pin', [ForumController::class, 'togglePin'])->name('forum.toggle-pin');
    Route::delete('forum/{post}', [ForumController::class, 'destroy'])->name('forum.destroy');

    Route::post('forum/{post}/comments', [ForumCommentController::class, 'store'])->name('forum.comments.store');
    Route::delete('forum/{post}/comments/{comment}', [ForumCommentController::class, 'destroy'])->name('forum.comments.destroy');

    Route::prefix('user-progress')->name('user-progress.')->group(function () {
        Route::get('/', [UserProgressController::class, 'index'])->name('index');
        Route::get('/export', [UserProgressController::class, 'export'])->name('export');
        Route::get('/chart-data', [UserProgressController::class, 'getChartData'])->name('chart-data');
        Route::get('/filter-options', [UserProgressController::class, 'getFilterOptions'])->name('filter-options');
    });
});
