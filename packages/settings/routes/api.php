<?php

use Illuminate\Support\Facades\Route;
use Vendor\Settings\Http\Controllers\Api\BannerController;

Route::prefix('banner')->group(function () {
    Route::get('/top', [BannerController::class, 'getBannerTop']);
    Route::get('/left', [BannerController::class, 'getBannerLeft']);
});

