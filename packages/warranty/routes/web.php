<?php

use Illuminate\Support\Facades\Route;
use Vendor\Warranty\Http\Controllers\Web\WarrantyClaimController;
use Vendor\Warranty\Http\Controllers\Web\WarrantyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Đây là các routes web cho package warranty
|
*/

// Admin Routes (requires authentication)
Route::prefix('admin/warranties')->name('admin.warranties.')->middleware(['auth'])->group(function () {
    // Warranty Management
    Route::get('/', [WarrantyController::class, 'index'])->name('index');
    Route::get('/product/{product}', [WarrantyController::class, 'show'])->name('show');
    Route::get('/product/{product}/print', [WarrantyController::class, 'bulkPrint'])->name('print');
    Route::post('/product/{product}/print', [WarrantyController::class, 'markBulkPrinted'])->name('print.mark');
    Route::get('/product/{product}/print-preview', [WarrantyController::class, 'printPreview'])->name('print.preview');
    Route::post('/product/{product}', [WarrantyController::class, 'store'])->name('store');
    Route::post('/product/{product}/qr-batch', [WarrantyController::class, 'generateQrBatch'])->name('qr-batch');
    Route::get('/{warranty}/edit', [WarrantyController::class, 'edit'])->name('edit');
    Route::put('/{warranty}', [WarrantyController::class, 'update'])->name('update');
    Route::delete('/{warranty}', [WarrantyController::class, 'destroy'])->name('destroy');
    Route::get('/{warranty}/qr', [WarrantyController::class, 'downloadQr'])->name('qr-download');
});

// Public claim routes
Route::get('/warranty/claim/{code}', [WarrantyClaimController::class, 'show'])->name('warranty.claim.show');
Route::post('/warranty/claim/{code}', [WarrantyClaimController::class, 'store'])->name('warranty.claim.store');
