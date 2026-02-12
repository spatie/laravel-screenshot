<?php

declare(strict_types=1);

namespace Spatie\LaravelScreenshot;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelScreenshot\Drivers\BrowsershotDriver;
use Spatie\LaravelScreenshot\Drivers\CloudflareDriver;
use Spatie\LaravelScreenshot\Drivers\ScreenshotDriver;

class ScreenshotServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-screenshot')
            ->hasConfigFile('laravel-screenshot');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('laravel-screenshot.driver.browsershot', function ($app) {
            return new BrowsershotDriver(
                config('laravel-screenshot.browsershot', []),
            );
        });

        $this->app->singleton('laravel-screenshot.driver.cloudflare', function ($app) {
            return new CloudflareDriver(
                config('laravel-screenshot.cloudflare', []),
            );
        });

        $this->app->bind(ScreenshotDriver::class, function ($app) {
            $driver = config('laravel-screenshot.driver', 'browsershot');

            return $app->make("laravel-screenshot.driver.{$driver}");
        });
    }
}
