<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    return redirect()->route('admin.dashboard');
});

// Load admin routes
require __DIR__ . '/admin.php';
