<?php


namespace Spatie\LaravelScreenshot\Support;

use Spatie\LaravelScreenshot\Facades\Screenshot;
use Spatie\LaravelScreenshot\ScreenshotBuilder;

function screenshot(?string $url = null): ScreenshotBuilder
{
    if ($url !== null) {
        return Screenshot::url($url);
    }

    return new ScreenshotBuilder;
}
