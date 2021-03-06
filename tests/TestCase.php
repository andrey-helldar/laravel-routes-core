<?php

namespace Tests;

use Helldar\LaravelRoutesCore\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\ExtendedTests\Assert;
use Tests\Fixtures\ServiceProvider as FixtureServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use Assert;

    protected function getPackageProviders($app)
    {
        return [
            FixtureServiceProvider::class,
            ServiceProvider::class,
        ];
    }
}
