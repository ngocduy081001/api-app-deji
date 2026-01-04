<?php

use Illuminate\Support\Facades\Route;
use Vendor\Product\Http\Controllers\Api\AttributeController;
use Vendor\Product\Http\Controllers\Api\AttributeValueController;
use Vendor\Product\Http\Controllers\Api\CategoryController;
use Vendor\Product\Http\Controllers\Api\ProductCategoryController;
use Vendor\Product\Http\Controllers\Api\ProductController;
use Vendor\Product\Http\Controllers\Api\ProductFlatController;
use Vendor\Product\Http\Controllers\Api\ProductVariantController;
use Vendor\Product\Http\Controllers\Api\SearchHistoryController;
use Vendor\Product\Http\Controllers\Api\PopularSearchController;
use Vendor\Product\Http\Controllers\Api\AdminPopularSearchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Product Management API routes for mobile apps, SPAs, and third-party integrations
|
*/

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show'])->name('show');
});

Route::prefix('categories/product/')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/featured', [CategoryController::class, 'featured'])->name('featured');
    Route::get('/{id}/products', [CategoryController::class, 'getProductByCategoryID'])->name('products');
});

Route::prefix('category/')->name('category.')->group(function () {
    Route::get('/{slug}', [CategoryController::class, 'getProductsByCategorySlug'])->name('products');
});

// Search History Routes (requires authentication)
Route::prefix('search')->middleware('auth:api')->group(function () {
    Route::get('/history', [SearchHistoryController::class, 'index']);
    Route::post('/history', [SearchHistoryController::class, 'store']);
    Route::delete('/history', [SearchHistoryController::class, 'destroy']);
});

// Popular Searches (public endpoint)
Route::prefix('search')->group(function () {
    Route::get('/popular', [PopularSearchController::class, 'index']);
});

// Admin Popular Searches API (requires authentication)
Route::prefix('admin/search/popular')->middleware('auth:api')->group(function () {
    Route::get('/', [AdminPopularSearchController::class, 'index']);
    Route::post('/', [AdminPopularSearchController::class, 'store']);
    Route::get('/{popularSearch}', [AdminPopularSearchController::class, 'show']);
    Route::put('/{popularSearch}', [AdminPopularSearchController::class, 'update']);
    Route::delete('/{popularSearch}', [AdminPopularSearchController::class, 'destroy']);
    Route::post('/update-order', [AdminPopularSearchController::class, 'updateOrder']);
    Route::post('/{popularSearch}/toggle-status', [AdminPopularSearchController::class, 'toggleStatus']);
});
