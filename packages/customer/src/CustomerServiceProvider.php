<?php

namespace Vendor\Customer;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/customer.php',
            'customer'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register menus
        $this->registerMenus();

        // Load routes
        Route::middleware('web')
            ->group(__DIR__ . '/../routes/web.php');

        Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/../routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'customer');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'customer');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/customer.php' => config_path('customer.php'),
        ], 'customer-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/customer'),
        ], 'customer-views');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'customer-migrations');
    }

    /**
     * Register menu items
     */
    protected function registerMenus(): void
    {
        \App\Services\MenuService::register([
            'title' => 'Khách hàng',
            'route' => 'admin.customers.index',
            'icon' => 'users',
            'order' => 15,
            'group' => 'main',
            'active' => ['admin.customers'],
            'children' => [
                [
                    'title' => 'Tất cả khách hàng',
                    'route' => 'admin.customers.index',
                    'active' => ['admin.customers.index', 'admin.customers.show', 'admin.customers.edit', 'admin.customers.create'],
                ],
            ],
        ]);
    }
}
