<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Customer Package Configuration
    |--------------------------------------------------------------------------
    |
    | Đây là file cấu hình cho package customer
    |
    */

    'enabled' => true,

    // Model class to use for customers
    'model' => \App\Models\Customer::class,

    // Pagination
    'per_page' => 20,
];
