<?php

namespace Spatie\LaravelScreenshot;

use Spatie\LaravelScreenshot\Enums\ImageType;

class ScreenshotOptions
{
    public function __construct(
        public ?int $width = null,
        public ?int $height = null,
        public ?ImageType $type = null,
        public ?int $quality = null,
        public ?bool $fullPage = null,
        public ?string $selector = null,
        public ?array $clip = null,
        public ?int $deviceScaleFactor = null,
        public ?bool $omitBackground = null,
        public ?int $waitForTimeout = null,
        public ?string $waitForSelector = null,
        public ?string $waitUntil = null,
    ) {}
}
