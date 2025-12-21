<?php

namespace App\Providers;

use App\Services\MenuService;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(MenuService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register core menus
        MenuService::register([
            'title' => 'Tổng quan',
            'route' => 'admin.dashboard',
            'icon' => 'home',
            'order' => 1,
            'group' => 'main',
            'active' => ['admin.dashboard'],
        ]);

        // This hook allows packages to register their menus
        // Packages should register menus in their ServiceProvider boot() method
    }
}
