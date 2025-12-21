<?php

namespace Vendor\Auth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/auth.php',
            'auth'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes with middleware
        Route::middleware('web')
            ->group(__DIR__ . '/../routes/web.php');

        Route::middleware('api')
            ->prefix('api')
            ->group(__DIR__ . '/../routes/api.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'auth');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'auth');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/auth.php' => config_path('auth.php'),
        ], 'auth-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/auth'),
        ], 'auth-views');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'auth-migrations');

        // Publish public assets
        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/auth'),
        ], 'auth-assets');

        // Register commands
        if ($this->app->runningInConsole()) {
            // $this->commands([
            //     Console\YourCommand::class,
            // ]);
        }
    }
}
