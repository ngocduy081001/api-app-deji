<?php

use Illuminate\Support\Facades\Route;
use Vendor\News\Http\Controllers\Api\ArticleController;
use Vendor\News\Http\Controllers\Api\NewsCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| News & Blog API routes for mobile apps, SPAs, and third-party integrations
|
*/

Route::prefix('/v1/news')->name('api.news.')->group(function () {

    // ==================== News Categories API Routes ====================
    Route::prefix('categories')->name('categories.')->group(function () {
        // List all categories
        Route::get('/', [NewsCategoryController::class, 'index'])->name('index');

        // Get category tree
        Route::get('/tree', [NewsCategoryController::class, 'tree'])->name('tree');

        // Create new category
        Route::post('/', [NewsCategoryController::class, 'store'])->name('store');

        // Get single category by ID
        Route::get('/{id}', [NewsCategoryController::class, 'show'])->name('show')
            ->where('id', '[0-9]+');

        // Get single category by slug
        Route::get('/slug/{slug}', [NewsCategoryController::class, 'showBySlug'])->name('show-by-slug');

        // Update category
        Route::put('/{id}', [NewsCategoryController::class, 'update'])->name('update');
        Route::patch('/{id}', [NewsCategoryController::class, 'update'])->name('patch');

        // Delete category
        Route::delete('/{id}', [NewsCategoryController::class, 'destroy'])->name('destroy');

        // Restore soft deleted category
        Route::post('/{id}/restore', [NewsCategoryController::class, 'restore'])->name('restore');
    });

    // ==================== Articles API Routes ====================
    Route::prefix('articles')->name('articles.')->group(function () {
        // List all articles
        Route::get('/', [ArticleController::class, 'index'])->name('index');

        // Get featured articles
        Route::get('/featured', [ArticleController::class, 'featured'])->name('featured');

        // Get most viewed articles
        Route::get('/most-viewed', [ArticleController::class, 'mostViewed'])->name('most-viewed');

        // Get recent articles
        Route::get('/recent', [ArticleController::class, 'recent'])->name('recent');

        // Create new article
        Route::post('/', [ArticleController::class, 'store'])->name('store');

        // Get single article by ID
        Route::get('/{id}', [ArticleController::class, 'show'])->name('show')
            ->where('id', '[0-9]+');

        // Get single article by slug
        Route::get('/slug/{slug}', [ArticleController::class, 'showBySlug'])->name('show-by-slug');

        // Get related articles
        Route::get('/{id}/related', [ArticleController::class, 'related'])->name('related');

        // Update article
        Route::put('/{id}', [ArticleController::class, 'update'])->name('update');
        Route::patch('/{id}', [ArticleController::class, 'update'])->name('patch');

        // Change article status
        Route::post('/{id}/status', [ArticleController::class, 'changeStatus'])->name('change-status');

        // Delete article
        Route::delete('/{id}', [ArticleController::class, 'destroy'])->name('destroy');

        // Restore soft deleted article
        Route::post('/{id}/restore', [ArticleController::class, 'restore'])->name('restore');
    });
});
