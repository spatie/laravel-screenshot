<?php

namespace Spatie\LaravelScreenshot\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelScreenshot\ScreenshotServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ScreenshotServiceProvider::class,
        ];
    }
}
