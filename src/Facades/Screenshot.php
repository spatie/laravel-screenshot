<?php

namespace Spatie\LaravelScreenshot\Facades;

use Illuminate\Support\Facades\Facade;
use Spatie\LaravelScreenshot\FakeScreenshotBuilder;
use Spatie\LaravelScreenshot\ScreenshotBuilder;
use Spatie\LaravelScreenshot\ScreenshotFactory;

/**
 * @mixin ScreenshotBuilder
 * @mixin FakeScreenshotBuilder
 */
class Screenshot extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ScreenshotFactory::class;
    }

    public static function fake(): void
    {
        static::swap(new FakeScreenshotBuilder);
    }
}
