<?php

namespace Vendor\Customer\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vendor\Customer\CustomerServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            CustomerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup database, etc.
    }
}
