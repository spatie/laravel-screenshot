<?php

namespace Spatie\LaravelScreenshot;

class ScreenshotFactory
{
    public function __call(string $method, array $parameters): mixed
    {
        return (new ScreenshotBuilder)->{$method}(...$parameters);
    }
}
