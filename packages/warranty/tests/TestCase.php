<?php

namespace Vendor\Warranty\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vendor\Warranty\WarrantyServiceProvider;

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
            WarrantyServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup environment nếu cần
    }
}
