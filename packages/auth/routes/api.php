<?php

use Illuminate\Support\Facades\Route;
use Vendor\Auth\Http\Controllers\Api\LoginController;
use Vendor\Auth\Http\Controllers\Api\RegisterController;
use Vendor\Auth\Http\Controllers\Api\GoogleLoginController;
use Vendor\Auth\Http\Controllers\Api\OtpController;
use Vendor\Auth\Http\Controllers\Api\AuthorizationController;
use Vendor\Auth\Http\Controllers\Api\TokenController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Authentication API routes for mobile apps, SPAs, and third-party integrations
| Using Laravel Passport for OAuth2 authentication
|
*/

// Authentication routes (v1/auth)
Route::prefix('v1/auth')->name('api.auth.')->group(function () {
    // OTP routes (public)
    Route::post('/check-email', [OtpController::class, 'checkEmail'])->name('check-email');
    Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('send-otp');
    Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('verify-otp');

    // OAuth 2.0 Authorization Code + PKCE flow (public client)
    Route::post('/authorize', [AuthorizationController::class, 'authorize'])->name('authorize');
    Route::post('/token', [TokenController::class, 'token'])->name('token');

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
