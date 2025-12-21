<?php

namespace Vendor\User;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/user.php',
            'user'
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'user');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'user');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/user.php' => config_path('user.php'),
        ], 'user-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/user'),
        ], 'user-views');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'user-migrations');
    }

    /**
     * Register menu items
     */
    protected function registerMenus(): void
    {
        \App\Services\MenuService::register([
            'title' => 'Tài khoản',
            'route' => 'admin.users.index',
            'icon' => 'users',
            'order' => 10,
            'group' => 'main',
            'active' => ['admin.users'],
            'children' => [
                [
                    'title' => 'Tất cả tài khoản',
                    'route' => 'admin.users.index',
                    'active' => ['admin.users.index', 'admin.users.show', 'admin.users.edit', 'admin.users.create'],
                ],
            ],
        ]);
    }
}
