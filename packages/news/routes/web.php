<?php

use Illuminate\Support\Facades\Route;
use Vendor\News\Http\Controllers\Web\ArticleController;
use Vendor\News\Http\Controllers\Web\NewsCategoryController;
use Vendor\News\Http\Controllers\Web\BlogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Traditional web routes for browser-based applications
|
*/

// Public Blog Routes (Frontend)
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/featured', [BlogController::class, 'featured'])->name('featured');
    Route::get('/most-viewed', [BlogController::class, 'mostViewed'])->name('most-viewed');
    Route::get('/category/{slug}', [BlogController::class, 'category'])->name('category');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
});

// Admin Routes (requires authentication)
Route::prefix('admin/news')->middleware(['auth'])->group(function () {

    // Article Management
    Route::prefix('articles')->name('admin.articles.')->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('index');
        Route::get('/create', [ArticleController::class, 'create'])->name('create');
        Route::post('/', [ArticleController::class, 'store'])->name('store');
        Route::get('/{article}', [ArticleController::class, 'show'])->name('show');
        Route::get('/{article}/edit', [ArticleController::class, 'edit'])->name('edit');
        Route::put('/{article}', [ArticleController::class, 'update'])->name('update');
        Route::delete('/{article}', [ArticleController::class, 'destroy'])->name('destroy');
    });

    // News Category Management
    Route::prefix('categories')->name('admin.news-categories.')->group(function () {
        Route::get('/', [NewsCategoryController::class, 'index'])->name('index');
        Route::get('/create', [NewsCategoryController::class, 'create'])->name('create');
        Route::post('/', [NewsCategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [NewsCategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [NewsCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [NewsCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [NewsCategoryController::class, 'destroy'])->name('destroy');
    });
});
