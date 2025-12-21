<?php

use Illuminate\Support\Facades\Route;
use Vendor\Order\Http\Controllers\Web\BookingController;
use Vendor\Order\Http\Controllers\Web\OrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Đây là các routes web cho package order
|
*/

// Admin Routes (requires authentication)
Route::prefix('admin/orders')->name('admin.orders.')->middleware('auth')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/create', [OrderController::class, 'create'])->name('create');
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
    Route::put('/{order}', [OrderController::class, 'update'])->name('update');
    Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
});

// Admin Booking Routes
Route::prefix('admin/bookings')->name('admin.bookings.')->middleware('auth')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
});
