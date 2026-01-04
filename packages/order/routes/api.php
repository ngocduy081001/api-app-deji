<?php

use Illuminate\Support\Facades\Route;
use Vendor\Order\Http\Controllers\Api\CustomerAddressController;
use Vendor\Order\Http\Controllers\Api\OrderController;
use Vendor\Order\Http\Controllers\Api\BookingController;
use Vendor\Order\Http\Controllers\Api\WarrantyController;
use Vendor\Order\Http\Controllers\Api\ShowroomController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Đây là các routes API cho package order
|
*/

// Customer Addresses API Routes - Protected with Passport
Route::prefix('customer-addresses')->middleware('auth:api')->group(function () {
    Route::get('/', [CustomerAddressController::class, 'index']);
    Route::post('/', [CustomerAddressController::class, 'store']);
    Route::get('/{id}', [CustomerAddressController::class, 'show']);
    Route::put('/{id}', [CustomerAddressController::class, 'update']);
    Route::delete('/{id}', [CustomerAddressController::class, 'destroy']);
});

// Orders API Routes - Protected with Passport
Route::prefix('orders')->middleware('auth:api')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::put('/{id}', [OrderController::class, 'update']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});

// Bookings API Routes - Protected with Passport
Route::prefix('bookings')->middleware('auth:api')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::post('/', [BookingController::class, 'store']);
    Route::get('/{id}', [BookingController::class, 'show']);
    Route::put('/{id}', [BookingController::class, 'update']);
    Route::delete('/{id}', [BookingController::class, 'destroy']);
});

// Warranties API Routes
Route::prefix('warranties')->group(function () {
    // Public lookup routes (no auth required)
    Route::get('/lookup/code', [WarrantyController::class, 'lookupByCode']);
    Route::get('/lookup/phone', [WarrantyController::class, 'lookupByPhone']);
    
    // Protected routes (require Passport authentication)
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [WarrantyController::class, 'index']);
        Route::post('/activate', [WarrantyController::class, 'activate']);
        Route::get('/{id}', [WarrantyController::class, 'show']);
        Route::put('/{id}', [WarrantyController::class, 'update']);
        Route::delete('/{id}', [WarrantyController::class, 'destroy']);
    });
});

// Showrooms API Routes
Route::prefix('showrooms')->group(function () {
    Route::get('/', [ShowroomController::class, 'index']);
    Route::get('/{id}', [ShowroomController::class, 'show']);
});
