<?php

namespace Spatie\LaravelScreenshot\Actions;

use Spatie\LaravelScreenshot\Enums\ImageType;

class DetermineImageType
{
    public function __invoke(string $path): ImageType
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $default = ImageType::tryFrom(config('laravel-screenshot.defaults.type', 'png')) ?? ImageType::Png;

        return match ($extension) {
            'png' => ImageType::Png,
            'jpg', 'jpeg' => ImageType::Jpeg,
            'webp' => ImageType::Webp,
            default => $default,
        };
    }
}
