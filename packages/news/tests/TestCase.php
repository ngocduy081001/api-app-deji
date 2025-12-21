<?php

namespace Vendor\News\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vendor\News\NewsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Thêm setup code ở đây
    }

    protected function getPackageProviders($app)
    {
        return [
            NewsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup environment nếu cần
    }
}
