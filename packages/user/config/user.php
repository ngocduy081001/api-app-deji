<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Package Configuration
    |--------------------------------------------------------------------------
    |
    | Đây là file cấu hình cho package user
    |
    */

    'enabled' => true,

    // Model class to use for users
    'model' => \App\Models\User::class,

    // Pagination
    'per_page' => 20,
];
