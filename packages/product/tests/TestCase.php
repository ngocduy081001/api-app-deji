<?php

namespace Vendor\Product\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vendor\Product\ProductServiceProvider;

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
            ProductServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup environment nếu cần
    }
}
