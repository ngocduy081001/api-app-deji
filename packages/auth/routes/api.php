<?php

use Illuminate\Support\Facades\Route;
use Vendor\Auth\Http\Controllers\Api\LoginController;
use Vendor\Auth\Http\Controllers\Api\RegisterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Authentication API routes for mobile apps, SPAs, and third-party integrations
|
*/

Route::prefix('v1/auth')->name('api.auth.')->group(function () {
    // Public routes (không yêu cầu authentication)
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/refresh', [LoginController::class, 'refresh'])->name('refresh');

    // Protected routes (yêu cầu access token)
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::post('/logout-all', [LoginController::class, 'logoutAll'])->name('logout-all');
        Route::get('/me', [LoginController::class, 'me'])->name('me');
    });
});
