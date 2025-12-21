<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\MenuServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    Vendor\Auth\AuthServiceProvider::class,
    Vendor\Customer\CustomerServiceProvider::class,
    Vendor\News\NewsServiceProvider::class,
    Vendor\Product\ProductServiceProvider::class,
     Vendor\Settings\SettingsServiceProvider::class,
    Vendor\User\UserServiceProvider::class,
    Vendor\Warranty\WarrantyServiceProvider::class,
    Vendor\Order\OrderServiceProvider::class,
];
