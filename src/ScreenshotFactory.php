<?php

namespace Spatie\LaravelScreenshot;

class ScreenshotFactory
{
    protected static ?ScreenshotBuilder $defaultScreenshotBuilder = null;

    public function __call(string $method, array $parameters): mixed
    {
        $builder = static::$defaultScreenshotBuilder
            ? clone static::$defaultScreenshotBuilder
            : new ScreenshotBuilder;

        return $builder->{$method}(...$parameters);
    }

    public static function default(): ScreenshotBuilder
    {
        static::$defaultScreenshotBuilder = new ScreenshotBuilder;

        return static::$defaultScreenshotBuilder;
    }

    public static function resetDefaultBuilder(): void
    {
        static::$defaultScreenshotBuilder = null;
    }
}
