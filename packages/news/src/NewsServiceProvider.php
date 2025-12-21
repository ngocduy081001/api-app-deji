<?php

namespace Vendor\News;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class NewsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/news.php',
            'news'
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'news');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'news');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/news.php' => config_path('news.php'),
        ], 'news-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/news'),
        ], 'news-views');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'news-migrations');

        // Publish public assets
        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/news'),
        ], 'news-assets');

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
            'title' => 'Tin tức',
            'route' => 'admin.articles.index',
            'icon' => 'news',
            'order' => 20,
            'group' => 'main',
            'active' => ['admin.articles', 'admin.news-categories'],
            'children' => [
                [
                    'title' => 'Tất cả bài viết',
                    'route' => 'admin.articles.index',
                    'active' => ['admin.articles.index', 'admin.articles.show', 'admin.articles.edit'],
                ],
                [
                    'title' => 'Tạo bài viết',
                    'route' => 'admin.articles.create',
                    'active' => ['admin.articles.create'],
                ],
                [
                    'title' => 'Chuyên mục',
                    'route' => 'admin.news-categories.index',
                    'active' => ['admin.news-categories'],
                ],
            ],
        ]);
    }
}
