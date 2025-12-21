<?php

namespace Vendor\Order\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vendor\Order\OrderServiceProvider;

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
            OrderServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup environment nếu cần
    }
}
