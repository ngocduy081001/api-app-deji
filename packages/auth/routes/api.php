<?php

use Illuminate\Support\Facades\Route;
use Vendor\Auth\Http\Controllers\Api\LoginController;
use Vendor\Auth\Http\Controllers\Api\RegisterController;
use Vendor\Auth\Http\Controllers\Api\GoogleLoginController;
use Vendor\Auth\Http\Controllers\Api\OtpController;
use Vendor\Auth\Http\Controllers\Api\JwtLoginController;
use Vendor\Auth\Http\Controllers\Api\JwtRegisterController;
use Vendor\Auth\Http\Controllers\Api\JwtAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Authentication API routes for mobile apps, SPAs, and third-party integrations
|
*/

// Passport OAuth routes (v1/auth)
Route::prefix('v1/auth')->name('api.auth.')->group(function () {
    // Public routes (không yêu cầu authentication)
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/google', [GoogleLoginController::class, 'login'])->name('google');
    Route::post('/google/user-info', [GoogleLoginController::class, 'loginWithUserInfo'])->name('google.user-info');
    Route::post('/refresh', [LoginController::class, 'refresh'])->name('refresh');

    // Protected routes (yêu cầu access token)
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::post('/logout-all', [LoginController::class, 'logoutAll'])->name('logout-all');
        Route::get('/me', [LoginController::class, 'me'])->name('me');
    });
});

// JWT Authentication routes (auth) - for mobile app
Route::prefix('auth')->name('api.auth.jwt.')->group(function () {
    // OTP routes
    Route::post('/check-email', [OtpController::class, 'checkEmail'])->name('check-email');
    Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('send-otp');
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('verify-otp');
    
    // Public routes
    Route::post('/register', [JwtRegisterController::class, 'register'])->name('register');
    Route::post('/login', [JwtLoginController::class, 'login'])->name('login');
    Route::post('/refresh', [JwtAuthController::class, 'refresh'])->name('refresh');

    // Protected routes (yêu cầu JWT token)
    Route::middleware('auth.api')->group(function () {
        Route::post('/logout', [JwtAuthController::class, 'logout'])->name('logout');
        Route::post('/logout-all', [JwtAuthController::class, 'logoutAll'])->name('logout-all');
        Route::get('/me', [JwtAuthController::class, 'me'])->name('me');
    });
});
