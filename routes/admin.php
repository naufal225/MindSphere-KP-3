<?php

use App\Http\Controllers\Admin\ChallengeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\HabitController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('users', UserController::class);

    Route::resource('school_classes', SchoolClassController::class);

    Route::resource('categories', CategoryController::class);

    Route::resource('challenges', ChallengeController::class);

    Route::resource('habits', HabitController::class);
});
