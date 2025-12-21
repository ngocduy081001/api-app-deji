<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration file for the settings package
    |
    */

    'cache' => [
        'enabled' => env('SETTINGS_CACHE_ENABLED', true),
        'ttl' => env('SETTINGS_CACHE_TTL', 3600),
    ],

    'tables' => [
        'settings' => 'settings',
        'menus' => 'menus',
        'banners' => 'banners',
        'slides' => 'slides',
    ],
];
