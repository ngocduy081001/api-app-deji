<?php

namespace Vendor\Settings;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Vendor\Settings\Models\Setting;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/settings.php',
            'settings'
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'settings');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'settings');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/settings.php' => config_path('settings.php'),
        ], 'settings-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/settings'),
        ], 'settings-views');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'settings-migrations');

        // Publish seeders
        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'settings-seeders');

        // Publish all (config, views, migrations, seeders)
        $this->publishes([
            __DIR__ . '/../config/settings.php' => config_path('settings.php'),
            __DIR__ . '/../resources/views' => resource_path('views/vendor/settings'),
            __DIR__ . '/../database/migrations' => database_path('migrations'),
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'settings');

        $this->bootSettingsCache();
    }

    /**
     * Register menu items
     */
    protected function registerMenus(): void
    {
        \App\Services\MenuService::register([
            'title' => 'Cài đặt',
            'route' => 'admin.settings.index',
            'icon' => 'settings',
            'order' => 100,
            'group' => 'main',
            'active' => ['admin.settings', 'admin.menus', 'admin.banners', 'admin.slides', 'admin.popular-searches'],
            'children' => [
                [
                    'title' => 'Cài đặt hệ thống',
                    'route' => 'admin.settings.index',
                    'active' => ['admin.settings'],
                ],
                [
                    'title' => 'Menu',
                    'route' => 'admin.menus.index',
                    'active' => ['admin.menus'],
                ],
                [
                    'title' => 'Banner',
                    'route' => 'admin.banners.index',
                    'active' => ['admin.banners'],
                ],
                [
                    'title' => 'Slide',
                    'route' => 'admin.slides.index',
                    'active' => ['admin.slides'],
                ],
                [
                    'title' => 'Tìm kiếm phổ biến',
                    'route' => 'admin.popular-searches.index',
                    'active' => ['admin.popular-searches'],
                ],
            ],
        ]);
    }

    /**
     * Load settings from database and push into config().
     */
    protected function bootSettingsCache(): void
    {
        if (!config('settings.loader.enabled', true)) {
            return;
        }

        try {
            if (!Schema::hasTable(config('settings.tables.settings', 'settings'))) {
                return;
            }
        } catch (\Throwable $th) {
            return;
        }

        $cacheEnabled = config('settings.cache.enabled', true);
        $cacheKey = config('settings.cache.key', 'settings.cache.all');
        $ttl = config('settings.cache.ttl', 3600);
        $configRoot = config('settings.loader.config_key', 'site');

        $settings = $cacheEnabled
            ? Cache::remember($cacheKey, $ttl, fn () => Setting::all())
            : Setting::all();

        $flat = $settings->pluck('value', 'key')->toArray();
        $grouped = $settings
            ->groupBy(function ($setting) {
                return $setting->group ?: 'general';
            })
            ->map(function ($items) {
                return $items->pluck('value', 'key')->toArray();
            })
            ->toArray();

        Config::set($configRoot, [
            'flat' => $flat,
            'groups' => $grouped,
        ]);

        foreach (config('settings.groups_map', []) as $group => $keys) {
            $values = [];

            foreach ($keys as $alias => $key) {
                if (is_int($alias)) {
                    $alias = $key;
                }

                $values[$alias] = $flat[$key] ?? null;
            }

            Config::set("{$configRoot}.{$group}", $values);
        }
    }
}
