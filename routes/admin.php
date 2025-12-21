<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Core admin routes
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\APIController::class, 'getNotifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\APIController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\APIController::class, 'markAllNotificationsAsRead'])->name('notifications.read-all');

    // File Manager routes are handled by laravel-filemanager package
});
