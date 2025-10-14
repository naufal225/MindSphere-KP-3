<?php

use Illuminate\Support\Facades\Route;

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/admin.php';

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('login');
});

