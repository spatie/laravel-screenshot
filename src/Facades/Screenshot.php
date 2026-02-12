<?php


namespace Spatie\LaravelScreenshot\Facades;

use Illuminate\Support\Facades\Facade;
use Spatie\LaravelScreenshot\FakeScreenshotBuilder;
use Spatie\LaravelScreenshot\ScreenshotFactory;

/**
 * @mixin \Spatie\LaravelScreenshot\ScreenshotBuilder
 * @mixin \Spatie\LaravelScreenshot\FakeScreenshotBuilder
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
