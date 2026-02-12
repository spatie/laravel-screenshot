<?php

namespace Spatie\LaravelScreenshot\Exceptions;

use Exception;

class InvalidDriver extends Exception
{
    public static function unknown(string $driverName): self
    {
        return new self("Unknown screenshot driver [{$driverName}].");
    }
}
