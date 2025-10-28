<?php

use App\Http\Controllers\Api\Ortu\ProfileController;
use App\Http\Controllers\Api\Ortu\ChildProgressController;
use App\Http\Controllers\Api\Ortu\DashboardController;
use App\Http\Controllers\Api\Ortu\SupportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:ortu'])->prefix('ortu')->name('ortu.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/child/{childId}', [DashboardController::class, 'getChildDetail']);

    Route::get('/child-progress', [ChildProgressController::class, 'getAllChildrenProgress']);
    Route::get('/child-progress/{childId}', [ChildProgressController::class, 'getChildProgress']);

     // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);
    Route::post('/profile/password', [ProfileController::class, 'changePassword']);

    Route::post('/supports', [SupportController::class, 'store']);
    Route::get('/supports', [SupportController::class, 'index']);
    Route::get('/supports/{id}', [SupportController::class, 'show']);
});


