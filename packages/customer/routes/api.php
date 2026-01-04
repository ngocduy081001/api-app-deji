<?php

use Illuminate\Support\Facades\Route;
use Vendor\Customer\Http\Controllers\Api\ProfileController;
use Vendor\Customer\Http\Controllers\Api\UserSettingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Đây là các routes API cho package customer
|
*/

// Profile API Routes - Protected with Passport
Route::prefix('profile')->middleware('auth:api')->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::post('/', [ProfileController::class, 'store']);
    Route::put('/', [ProfileController::class, 'update']);

    // Avatar upload endpoints - dedicated for clarity
    Route::post('/avatar', [ProfileController::class, 'uploadAvatar']); // POST /api/profile/avatar
    Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']); // DELETE /api/profile/avatar
});

// User Settings API Routes - Protected with Passport
Route::middleware('auth:api')->group(function () {
    Route::get('/settings', [UserSettingController::class, 'index']);
    Route::put('/settings', [UserSettingController::class, 'update']);
});
