---
title: Custom drivers
weight: 4
---

You can create custom screenshot drivers by implementing the `ScreenshotDriver` interface.

## Creating a driver

A screenshot driver must implement two methods:

```php
use Spatie\LaravelScreenshot\Drivers\ScreenshotDriver;
use Spatie\LaravelScreenshot\ScreenshotOptions;

class PlaywrightDriver implements ScreenshotDriver
{
    public function __construct(protected array $config = [])
    {
        // ...
    }

    public function generateScreenshot(
        string $input,
        bool $isHtml,
        ScreenshotOptions $options
    ): string {
        // Take the screenshot and return raw image bytes
    }

    public function saveScreenshot(
        string $input,
        bool $isHtml,
        ScreenshotOptions $options,
        string $path
    ): void {
        // Take the screenshot and save it to $path
    }
}
```

The `$input` parameter is either a URL or an HTML string, depending on the value of `$isHtml`.

The `ScreenshotOptions` object contains all the rendering options:

- `$options->width` — viewport width
- `$options->height` — viewport height
- `$options->type` — `ImageType` enum (Png, Jpeg, Webp)
- `$options->quality` — image quality (0-100, for JPEG/WebP)
- `$options->fullPage` — whether to capture the full scrollable page
- `$options->selector` — CSS selector to capture
- `$options->clip` — clip region (`['x' => int, 'y' => int, 'width' => int, 'height' => int]`)
- `$options->deviceScaleFactor` — device pixel ratio
- `$options->omitBackground` — whether to omit the background
- `$options->waitForTimeout` — milliseconds to wait
- `$options->waitForSelector` — CSS selector to wait for
- `$options->waitUntil` — network event to wait for

## Registering the driver

Register your driver as a singleton in a service provider:

```php
use App\Screenshots\PlaywrightDriver;

// in a service provider

$this->app->singleton('laravel-screenshot.driver.playwright', function () {
    return new PlaywrightDriver(config('laravel-screenshot.playwright', []));
});
```

## Using the driver

Once registered, you can use it per-screenshot:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->driver('playwright')
    ->save('screenshot.png');
```

Or set it as the default in your config file:

```php
// config/laravel-screenshot.php

'driver' => 'playwright',
```
