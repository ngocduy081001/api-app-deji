<?php

namespace Vendor\Order;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/order.php',
            'order'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register menus
        $this->registerMenus();

        // Load routes with middleware
        Route::middleware('web')
            ->group(__DIR__ . '/../routes/web.php');

        Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/../routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'order');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'order');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/order.php' => config_path('order.php'),
        ], 'order-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/order'),
        ], 'order-views');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'order-migrations');

        // Publish public assets
        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/order'),
        ], 'order-assets');

        // Register commands
        if ($this->app->runningInConsole()) {
            // $this->commands([
            //     Console\YourCommand::class,
            // ]);
        }
    }

    /**
     * Register menu items
     */
    protected function registerMenus(): void
    {
        \App\Services\MenuService::register([
            'title' => 'Đơn hàng',
            'route' => 'admin.orders.index',
            'icon' => 'orders',
            'order' => 10,
            'group' => 'main',
            'active' => ['admin.orders'],
        ]);

        \App\Services\MenuService::register([
            'title' => 'Booking',
            'route' => 'admin.bookings.index',
            'icon' => 'booking',
            'order' => 11,
            'group' => 'main',
            'active' => ['admin.bookings'],
        ]);
    }
}
