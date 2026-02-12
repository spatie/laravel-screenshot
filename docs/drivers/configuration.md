---
title: Configuration
weight: 1
---

This package supports multiple screenshot drivers. You can set the default driver via the `LARAVEL_SCREENSHOT_DRIVER` environment variable:

```env
LARAVEL_SCREENSHOT_DRIVER=browsershot
```

Or in the config file:

```php
// config/laravel-screenshot.php

return [
    'driver' => 'browsershot', // or 'cloudflare'
    // ...
];
```

## Switching drivers per screenshot

You can switch drivers for a specific screenshot using the `driver()` method:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->driver('cloudflare')
    ->save('screenshot.png');
```

## Using a custom driver instance

You can also pass a driver instance directly:

```php
use Spatie\LaravelScreenshot\Drivers\BrowsershotDriver;
use Spatie\LaravelScreenshot\Facades\Screenshot;

$driver = new BrowsershotDriver([
    'chrome_path' => '/usr/bin/google-chrome',
]);

Screenshot::url('https://example.com')
    ->setDriver($driver)
    ->save('screenshot.png');
```
