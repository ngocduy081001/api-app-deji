<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakePackageCommand extends Command
{
    protected $signature = 'make:package 
                          {name : Tên package (vd: demo-package)}
                          {--vendor= : Vendor name (mặc định: vendor)}
                          {--namespace= : Namespace (mặc định: từ vendor và name)}
                          {--description= : Mô tả package}
                          {--author= : Tên tác giả}
                          {--email= : Email tác giả}';

    protected $description = 'Tạo cấu trúc cơ bản cho một Laravel package mới';

    protected $packageName;
    protected $vendorName;
    protected $namespace;
    protected $packagePath;

    public function handle()
    {
        $this->packageName = $this->argument('name');
        $this->vendorName = $this->option('vendor') ?: 'vendor';
        
        // Tạo namespace từ vendor và package name
        $this->namespace = $this->option('namespace') ?: 
            Str::studly($this->vendorName) . '\\' . Str::studly(str_replace('-', '', $this->packageName));

        $this->packagePath = base_path('packages/' . $this->packageName);

        if (File::exists($this->packagePath)) {
            $this->error("Package '{$this->packageName}' đã tồn tại!");
            return 1;
        }

        $this->info("Đang tạo package: {$this->packageName}");
        $this->newLine();

        // Tạo cấu trúc thư mục
        $this->createDirectoryStructure();

        // Tạo các file cơ bản
        $this->createComposerJson();
        $this->createServiceProvider();
        $this->createConfigFile();
        $this->createRoutesFiles();
        $this->createReadme();
        $this->createGitignore();
        $this->createPhpUnitXml();
        $this->createTestCase();
        $this->createChangelog();
        $this->createLicense();

        $this->newLine();
        $this->info('✓ Package đã được tạo thành công!');
        $this->newLine();
        $this->info('Các bước tiếp theo:');
        $this->line("1. Cập nhật composer.json gốc để autoload package");
        $this->line("2. Chạy: composer dump-autoload");
        $this->line("3. Thêm ServiceProvider vào config/app.php (nếu cần)");
        $this->line("4. Package của bạn ở: packages/{$this->packageName}");

        return 0;
    }

    protected function createDirectoryStructure()
    {
        $directories = [
            'src',
            'src/Console',
            'src/Controllers',
            'src/Models',
            'src/Middleware',
            'src/Events',
            'src/Listeners',
            'src/Mail',
            'src/Notifications',
            'src/Facades',
            'config',
            'database/migrations',
            'database/factories',
            'database/seeders',
            'resources/views',
            'resources/lang/en',
            'routes',
            'tests/Feature',
            'tests/Unit',
            'public/css',
            'public/js',
        ];

        foreach ($directories as $dir) {
            File::makeDirectory($this->packagePath . '/' . $dir, 0755, true);
            $this->line("  ✓ Tạo thư mục: {$dir}");
        }
    }

    protected function createComposerJson()
    {
        $vendorLower = Str::lower($this->vendorName);
        $packageLower = Str::lower($this->packageName);
        $description = $this->option('description') ?: "A Laravel package";
        $author = $this->option('author') ?: "Your Name";
        $email = $this->option('email') ?: "your.email@example.com";
        
        $namespace = str_replace('\\', '\\\\', $this->namespace);
        $providerClass = str_replace('\\', '\\\\', $this->namespace . '\\' . $this->getServiceProviderName());

        $content = <<<JSON
{
    "name": "{$vendorLower}/{$packageLower}",
    "description": "{$description}",
    "type": "library",
    "license": "MIT",
    "keywords": [
        "laravel",
        "package"
    ],
    "authors": [
        {
            "name": "{$author}",
            "email": "{$email}"
        }
    ],
    "require": {
        "php": "^8.1",
        "illuminate/support": "^10.0|^11.0"
    },
    "require-dev": {
        "orchestra/testbench": "^8.0|^9.0",
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "{$namespace}\\\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "{$namespace}\\\\Tests\\\\": "tests/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "{$providerClass}"
            ]
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}

JSON;

        File::put($this->packagePath . '/composer.json', $content);
        $this->line("  ✓ Tạo file: composer.json");
    }

    protected function createServiceProvider()
    {
        $providerName = $this->getServiceProviderName();
        $configName = Str::kebab($this->packageName);

        $content = <<<PHP
<?php

namespace {$this->namespace};

use Illuminate\Support\ServiceProvider;

class {$providerName} extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        \$this->mergeConfigFrom(
            __DIR__.'/../config/{$configName}.php',
            '{$configName}'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes
        \$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        \$this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Load views
        \$this->loadViewsFrom(__DIR__.'/../resources/views', '{$configName}');

        // Load migrations
        \$this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load translations
        \$this->loadTranslationsFrom(__DIR__.'/../resources/lang', '{$configName}');

        // Publish config
        \$this->publishes([
            __DIR__.'/../config/{$configName}.php' => config_path('{$configName}.php'),
        ], '{$configName}-config');

        // Publish views
        \$this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/{$configName}'),
        ], '{$configName}-views');

        // Publish migrations
        \$this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], '{$configName}-migrations');

        // Publish public assets
        \$this->publishes([
            __DIR__.'/../public' => public_path('vendor/{$configName}'),
        ], '{$configName}-assets');

        // Register commands
        if (\$this->app->runningInConsole()) {
            // \$this->commands([
            //     Console\\YourCommand::class,
            // ]);
        }
    }
}

PHP;

        File::put($this->packagePath . '/src/' . $providerName . '.php', $content);
        $this->line("  ✓ Tạo file: src/{$providerName}.php");
    }

    protected function createConfigFile()
    {
        $configName = Str::kebab($this->packageName);

        $content = <<<PHP
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Package Configuration
    |--------------------------------------------------------------------------
    |
    | Đây là file cấu hình cho package {$this->packageName}
    |
    */

    'enabled' => true,

    // Thêm các cấu hình khác ở đây
];

PHP;

        File::put($this->packagePath . '/config/' . $configName . '.php', $content);
        $this->line("  ✓ Tạo file: config/{$configName}.php");
    }

    protected function createRoutesFiles()
    {
        // Web routes
        $webContent = <<<PHP
<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Đây là các routes web cho package {$this->packageName}
|
*/

// Route::get('/example', function () {
//     return view('{$this->packageName}::index');
// });

PHP;

        File::put($this->packagePath . '/routes/web.php', $webContent);
        $this->line("  ✓ Tạo file: routes/web.php");

        // API routes
        $apiContent = <<<PHP
<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Đây là các routes API cho package {$this->packageName}
|
*/

// Route::prefix('api')->group(function () {
//     Route::get('/example', function () {
//         return response()->json(['message' => 'Hello from {$this->packageName}']);
//     });
// });

PHP;

        File::put($this->packagePath . '/routes/api.php', $apiContent);
        $this->line("  ✓ Tạo file: routes/api.php");
    }

    protected function createReadme()
    {
        $packageTitle = Str::title(str_replace('-', ' ', $this->packageName));
        $configName = Str::kebab($this->packageName);
        $description = $this->option('description') ?: 'A Laravel package';
        $vendorName = $this->vendorName;
        $packageName = $this->packageName;

        $content = <<<MD
# {$packageTitle}

{$description}

## Cài đặt

```bash
composer require {$vendorName}/{$packageName}
```

## Sử dụng

### Publish config

```bash
php artisan vendor:publish --tag={$configName}-config
```

### Publish views

```bash
php artisan vendor:publish --tag={$configName}-views
```

### Publish migrations

```bash
php artisan vendor:publish --tag={$configName}-migrations
php artisan migrate
```

### Publish assets

```bash
php artisan vendor:publish --tag={$configName}-assets
```

## Cấu hình

Sau khi publish config, bạn có thể cấu hình package trong file `config/{$configName}.php`

## Testing

```bash
composer test
```

## License

MIT License

MD;

        File::put($this->packagePath . '/README.md', $content);
        $this->line("  ✓ Tạo file: README.md");
    }

    protected function createGitignore()
    {
        $content = <<<TXT
/vendor
composer.lock
.phpunit.result.cache
.DS_Store
Thumbs.db
.idea
.vscode
*.log

TXT;

        File::put($this->packagePath . '/.gitignore', $content);
        $this->line("  ✓ Tạo file: .gitignore");
    }

    protected function createPhpUnitXml()
    {
        $namespaceEscaped = str_replace('\\', '\\\\', $this->namespace);

        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Package Test Suite">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
    </php>
    <source>
        <include>
            <directory suffix=".php">src</directory>
        </include>
    </source>
</phpunit>

XML;

        File::put($this->packagePath . '/phpunit.xml', $content);
        $this->line("  ✓ Tạo file: phpunit.xml");
    }

    protected function createTestCase()
    {
        $providerName = $this->getServiceProviderName();

        $content = <<<PHP
<?php

namespace {$this->namespace}\\Tests;

use Orchestra\\Testbench\\TestCase as Orchestra;
use {$this->namespace}\\{$providerName};

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Thêm setup code ở đây
    }

    protected function getPackageProviders(\$app)
    {
        return [
            {$providerName}::class,
        ];
    }

    protected function getEnvironmentSetUp(\$app)
    {
        // Setup environment nếu cần
    }
}

PHP;

        File::put($this->packagePath . '/tests/TestCase.php', $content);
        $this->line("  ✓ Tạo file: tests/TestCase.php");

        // Tạo example test
        $exampleTest = <<<PHP
<?php

namespace {$this->namespace}\\Tests\\Feature;

use {$this->namespace}\\Tests\\TestCase;

class ExampleTest extends TestCase
{
    /** @test */
    public function it_works()
    {
        \$this->assertTrue(true);
    }
}

PHP;

        File::put($this->packagePath . '/tests/Feature/ExampleTest.php', $exampleTest);
        $this->line("  ✓ Tạo file: tests/Feature/ExampleTest.php");
    }

    protected function createChangelog()
    {
        $content = <<<MD
# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Initial release

MD;

        File::put($this->packagePath . '/CHANGELOG.md', $content);
        $this->line("  ✓ Tạo file: CHANGELOG.md");
    }

    protected function createLicense()
    {
        $year = date('Y');
        $author = $this->option('author') ?: 'Your Name';

        $content = <<<TXT
MIT License

Copyright (c) {$year} {$author}

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

TXT;

        File::put($this->packagePath . '/LICENSE.md', $content);
        $this->line("  ✓ Tạo file: LICENSE.md");
    }

    protected function getServiceProviderName(): string
    {
        return Str::studly(str_replace('-', '', $this->packageName)) . 'ServiceProvider';
    }
}

