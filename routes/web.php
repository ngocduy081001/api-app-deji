<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    return redirect()->route('admin.dashboard');
});
Route::get('/demo', function () {
    return view('demo');
});

// Load admin routes
require __DIR__ . '/admin.php';
