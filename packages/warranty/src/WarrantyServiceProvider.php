<?php

namespace Vendor\Warranty;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class WarrantyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/warranty.php',
            'warranty'
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'warranty');

        // Register Livewire components
        Livewire::component('warranty::edit-warranty', \Vendor\Warranty\Http\Livewire\EditWarranty::class);

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'warranty');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/warranty.php' => config_path('warranty.php'),
        ], 'warranty-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/warranty'),
        ], 'warranty-views');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'warranty-migrations');

        // Publish public assets
        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/warranty'),
        ], 'warranty-assets');

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
            'title' => 'Bảo hành',
            'route' => 'admin.warranties.index',
            'icon' => 'shield-check',
            'order' => 20,
            'group' => 'main',
            'active' => ['admin.warranties'],
            'children' => [
                [
                    'title' => 'Tất cả bảo hành',
                    'route' => 'admin.warranties.index',
                    'active' => ['admin.warranties.index', 'admin.warranties.show', 'admin.warranties.edit'],
                ],
                // [
                //     'title' => 'Tạo mới',
                //     'route' => 'admin.warranties.create',
                //     'active' => ['admin.warranties.create'],
                // ],
            ],
        ]);
    }
}
