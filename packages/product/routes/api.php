<?php

use Illuminate\Support\Facades\Route;
use Vendor\Product\Http\Controllers\Api\AttributeController;
use Vendor\Product\Http\Controllers\Api\AttributeValueController;
use Vendor\Product\Http\Controllers\Api\CategoryController;
use Vendor\Product\Http\Controllers\Api\ProductCategoryController;
use Vendor\Product\Http\Controllers\Api\ProductController;
use Vendor\Product\Http\Controllers\Api\ProductFlatController;
use Vendor\Product\Http\Controllers\Api\ProductVariantController;

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
