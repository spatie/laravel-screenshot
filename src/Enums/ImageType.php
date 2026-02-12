<?php

namespace Spatie\LaravelScreenshot\Enums;

enum ImageType: string
{
    case Png = 'png';
    case Jpeg = 'jpeg';
    case Webp = 'webp';

    public function contentType(): string
    {
        return match ($this) {
            self::Png => 'image/png',
            self::Jpeg => 'image/jpeg',
            self::Webp => 'image/webp',
        };
    }
}
