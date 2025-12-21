<?php

namespace Vendor\User\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vendor\User\UserServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            UserServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup database, etc.
    }
}
