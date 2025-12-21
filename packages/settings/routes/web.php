<?php

use Illuminate\Support\Facades\Route;
use Vendor\Settings\Http\Controllers\Web\SettingsController;
use Vendor\Settings\Http\Controllers\Web\MenuController;
use Vendor\Settings\Http\Controllers\Web\MenuGroupController;
use Vendor\Settings\Http\Controllers\Web\BannerController;
use Vendor\Settings\Http\Controllers\Web\SlideController;
use Vendor\Settings\Http\Controllers\Web\SliderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Đây là các routes web cho package settings
|
*/

// Admin Routes (requires authentication)
Route::prefix('admin/settings')->name('admin.settings.')->middleware(['auth'])->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::put('/', [SettingsController::class, 'update'])->name('update');
});

Route::prefix('admin/menu-groups')->name('admin.menu-groups.')->middleware(['auth'])->group(function () {
    Route::get('/', [MenuGroupController::class, 'index'])->name('index');
    Route::get('/create', [MenuGroupController::class, 'create'])->name('create');
    Route::post('/', [MenuGroupController::class, 'store'])->name('store');
    Route::get('/{menuGroup}/edit', [MenuGroupController::class, 'edit'])->name('edit');
    Route::put('/{menuGroup}', [MenuGroupController::class, 'update'])->name('update');
    Route::delete('/{menuGroup}', [MenuGroupController::class, 'destroy'])->name('destroy');
});

Route::prefix('admin/menus')->name('admin.menus.')->middleware(['auth'])->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('index');
    Route::get('/create', [MenuController::class, 'create'])->name('create');
    Route::post('/', [MenuController::class, 'store'])->name('store');
    Route::post('/update-order', [MenuController::class, 'updateOrder'])->name('update-order');
    Route::get('/{menu}/edit', [MenuController::class, 'edit'])->name('edit');
    Route::put('/{menu}', [MenuController::class, 'update'])->name('update');
    Route::delete('/{menu}', [MenuController::class, 'destroy'])->name('destroy');
});

Route::prefix('admin/banners')->name('admin.banners.')->middleware(['auth'])->group(function () {
    Route::get('/', [BannerController::class, 'index'])->name('index');
    Route::get('/create', [BannerController::class, 'create'])->name('create');
    Route::post('/', [BannerController::class, 'store'])->name('store');
    Route::get('/{banner}/edit', [BannerController::class, 'edit'])->name('edit');
    Route::put('/{banner}', [BannerController::class, 'update'])->name('update');
    Route::delete('/{banner}', [BannerController::class, 'destroy'])->name('destroy');
});

Route::prefix('admin/sliders')->name('admin.sliders.')->middleware(['auth'])->group(function () {
    Route::get('/', [SliderController::class, 'index'])->name('index');
    Route::get('/create', [SliderController::class, 'create'])->name('create');
    Route::post('/', [SliderController::class, 'store'])->name('store');
    Route::get('/{slider}/edit', [SliderController::class, 'edit'])->name('edit');
    Route::put('/{slider}', [SliderController::class, 'update'])->name('update');
    Route::delete('/{slider}', [SliderController::class, 'destroy'])->name('destroy');
});

Route::prefix('admin/slides')->name('admin.slides.')->middleware(['auth'])->group(function () {
    Route::get('/', [SlideController::class, 'index'])->name('index');
    Route::post('/', [SlideController::class, 'store'])->name('store');
    Route::post('/update-order', [SlideController::class, 'updateOrder'])->name('update-order');
    Route::put('/{sliderItem}', [SlideController::class, 'update'])->name('update');
    Route::delete('/{sliderItem}', [SlideController::class, 'destroy'])->name('destroy');
});
