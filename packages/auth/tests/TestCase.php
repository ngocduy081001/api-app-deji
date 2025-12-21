<?php

namespace Vendor\Auth\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vendor\Auth\AuthServiceProvider;

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
            AuthServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup environment nếu cần
    }
}
