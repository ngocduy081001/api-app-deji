<?php

use Illuminate\Support\Facades\Route;
use Vendor\Customer\Http\Controllers\Web\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Đây là các routes web cho package customer
|
*/

// Admin Routes (requires authentication)
Route::prefix('admin/customers')->name('admin.customers.')->middleware(['auth'])->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/export', [CustomerController::class, 'export'])->name('export');
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::post('/', [CustomerController::class, 'store'])->name('store');
    Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
    Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
    Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
});
