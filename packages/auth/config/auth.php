<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Package Configuration
    |--------------------------------------------------------------------------
    |
    | Đây là file cấu hình cho package auth
    |
    */

    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Access Token Expiry (minutes)
    |--------------------------------------------------------------------------
    |
    | Thời gian hết hạn của access token tính bằng phút.
    | Mặc định: 15 phút
    |
    */
    'access_token_expiry' => env('AUTH_ACCESS_TOKEN_EXPIRY', 15),

    /*
    |--------------------------------------------------------------------------
    | Refresh Token Expiry (days)
    |--------------------------------------------------------------------------
    |
    | Thời gian hết hạn của refresh token tính bằng ngày.
    | Mặc định: 30 ngày
    |
    */
    'refresh_token_expiry' => env('AUTH_REFRESH_TOKEN_EXPIRY', 30),

    /*
    |--------------------------------------------------------------------------
    | Token Cleanup Schedule
    |--------------------------------------------------------------------------
    |
    | Tự động xóa các refresh token đã hết hạn. Có thể sử dụng
    | lệnh "php artisan passport:purge" trong scheduler của bạn.
    |
    */
    'cleanup_expired_tokens' => true,
];
