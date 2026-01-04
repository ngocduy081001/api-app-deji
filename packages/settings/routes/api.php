<?php

use Illuminate\Support\Facades\Route;
use Vendor\Settings\Http\Controllers\Api\BannerController;
use Vendor\Settings\Http\Controllers\Api\MenuController;

Route::prefix('banners')->group(function () {
    Route::get('/main', [BannerController::class, 'getMain']);
});


Route::prefix('menus')->group(function () {
    Route::get('/main', [MenuController::class, 'getMain']);
});
