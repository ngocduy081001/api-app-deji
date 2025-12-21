<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Package Configuration
    |--------------------------------------------------------------------------
    |
    | Đây là file cấu hình cho package warranty
    |
    */

    'enabled' => true,

    'default_months' => env('WARRANTY_DEFAULT_MONTHS', 12),

    'qr' => [
        'disk' => env('WARRANTY_QR_STORAGE_DISK', 'public'),
        'path' => env('WARRANTY_QR_STORAGE_PATH', 'warranty-qrs'),
        'size' => env('WARRANTY_QR_SIZE', 600),
        'margin' => env('WARRANTY_QR_MARGIN', 2),
    ],
];
