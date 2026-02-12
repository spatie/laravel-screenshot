<?php

declare(strict_types=1);

namespace Spatie\LaravelScreenshot\Drivers;

use Spatie\LaravelScreenshot\ScreenshotOptions;

interface ScreenshotDriver
{
    public function generateScreenshot(string $input, bool $isHtml, ScreenshotOptions $options): string;

    public function saveScreenshot(string $input, bool $isHtml, ScreenshotOptions $options, string $path): void;
}
