<?php

use Illuminate\Support\Facades\Route;
use Vendor\Product\Http\Controllers\Web\ProductController;
use Vendor\Product\Http\Controllers\Web\ProductCategoryController;
use Vendor\Product\Http\Controllers\Web\CatalogController;
use Vendor\Product\Http\Controllers\Web\PopularSearchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Traditional web routes for browser-based applications
|
*/

// Public Catalog Routes (Storefront)
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [CatalogController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [CatalogController::class, 'category'])->name('category');
    Route::get('/{slug}', [CatalogController::class, 'show'])->name('show');
});

// Admin Routes (requires authentication)
Route::prefix('admin/products')->name('admin.products.')->middleware(['auth'])->group(function () {
    // Product Management
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
});

Route::prefix('admin/categories')->name('admin.categories.')->middleware(['auth'])->group(function () {
    Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
    Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
    Route::post('/update-order', [ProductCategoryController::class, 'updateOrder'])->name('update-order');
    Route::get('/{category}', [ProductCategoryController::class, 'getCategory'])->name('get');
    Route::put('/{category}', [ProductCategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [ProductCategoryController::class, 'destroy'])->name('destroy');
});

// Admin Popular Searches Routes
Route::prefix('admin/popular-searches')->name('admin.popular-searches.')->middleware(['auth'])->group(function () {
    Route::get('/', [PopularSearchController::class, 'index'])->name('index');
    Route::post('/', [PopularSearchController::class, 'store'])->name('store');
    Route::post('/update-order', [PopularSearchController::class, 'updateOrder'])->name('update-order');
    Route::put('/{popularSearch}', [PopularSearchController::class, 'update'])->name('update');
    Route::post('/{popularSearch}/toggle-status', [PopularSearchController::class, 'toggleStatus'])->name('toggle-status');
    Route::delete('/{popularSearch}', [PopularSearchController::class, 'destroy'])->name('destroy');
});
