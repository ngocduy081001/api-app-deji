<?php

namespace Vendor\Product;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Vendor\Product\Models\Product;
use Vendor\Product\Models\ProductVariant;
use Vendor\Product\Models\AttributeValue;
use Vendor\Product\Observers\ProductObserver;
use Vendor\Product\Observers\ProductVariantObserver;
use Vendor\Product\Observers\AttributeValueObserver;
use Vendor\Product\Console\Commands\RebuildProductFlatsCommand;
use Vendor\Product\Console\Commands\ImportDataFromApiCommand;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/product.php',
            'product'
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
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'product');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'product');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/product.php' => config_path('product.php'),
        ], 'product-config');

        // Publish views
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/product'),
        ], 'product-views');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'product-migrations');

        // Publish public assets
        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/product'),
        ], 'product-assets');


        // Publish seeders
        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders'),
        ], 'product-seeders');

        // Register observers
        Product::observe(ProductObserver::class);
        ProductVariant::observe(ProductVariantObserver::class);
        AttributeValue::observe(AttributeValueObserver::class);

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                RebuildProductFlatsCommand::class,
                ImportDataFromApiCommand::class,
            ]);
        }
    }

    /**
     * Register menu items
     */
    protected function registerMenus(): void
    {
        \App\Services\MenuService::register([
            'title' => 'Sản phẩm',
            'route' => 'admin.products.index',
            'icon' => 'products',
            'order' => 10,
            'group' => 'main',
            'active' => ['admin.products', 'admin.categories'],
            'children' => [
                [
                    'title' => 'Tất cả sản phẩm',
                    'route' => 'admin.products.index',
                    'active' => ['admin.products.index', 'admin.products.show', 'admin.products.edit'],
                ],
                [
                    'title' => 'Tạo mới',
                    'route' => 'admin.products.create',
                    'active' => ['admin.products.create'],
                ],
                [
                    'title' => 'Danh mục',
                    'route' => 'admin.categories.index',
                    'active' => ['admin.categories'],
                ],
            ],
        ]);
    }
}
