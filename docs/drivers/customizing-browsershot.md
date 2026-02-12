---
title: Customizing Browsershot
weight: 2
---

The Browsershot driver uses [spatie/browsershot](https://spatie.be/docs/browsershot) under the hood. You can customize its behavior globally via the config file or per-screenshot using the `withBrowsershot()` method.

## Global configuration

The config file lets you set binary paths and other Browsershot defaults:

```php
// config/laravel-screenshot.php

'browsershot' => [
    'node_binary' => env('LARAVEL_SCREENSHOT_NODE_BINARY'),
    'npm_binary' => env('LARAVEL_SCREENSHOT_NPM_BINARY'),
    'chrome_path' => env('LARAVEL_SCREENSHOT_CHROME_PATH'),
    'node_modules_path' => env('LARAVEL_SCREENSHOT_NODE_MODULES_PATH'),
    'bin_path' => env('LARAVEL_SCREENSHOT_BIN_PATH'),
    'temp_path' => env('LARAVEL_SCREENSHOT_TEMP_PATH'),
    'no_sandbox' => env('LARAVEL_SCREENSHOT_NO_SANDBOX', false),
],
```

## Per-screenshot customization

For one-off customizations, use the `withBrowsershot()` method. This gives you direct access to the underlying Browsershot instance:

```php
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->withBrowsershot(function (Browsershot $browsershot) {
        $browsershot
            ->setExtraHttpHeaders(['Authorization' => 'Bearer token'])
            ->userAgent('My Custom User Agent');
    })
    ->save('screenshot.png');
```

You can use any method available on the [Browsershot](https://spatie.be/docs/browsershot) instance:

```php
Screenshot::url('https://example.com')
    ->withBrowsershot(function (Browsershot $browsershot) {
        $browsershot
            ->setExtraHttpHeaders(['Cookie' => 'session=abc123'])
            ->dismissDialogs()
            ->timeout(60);
    })
    ->save('screenshot.png');
```

## Running in no-sandbox mode

If you're running in a Docker container or other restricted environment, you may need to run Chrome without sandboxing:

```env
LARAVEL_SCREENSHOT_NO_SANDBOX=true
```

Or per-screenshot:

```php
Screenshot::url('https://example.com')
    ->withBrowsershot(fn (Browsershot $browsershot) => $browsershot->noSandbox())
    ->save('screenshot.png');
```

## Limitations

The `withBrowsershot()` method cannot be used together with `saveQueued()`. Closures cannot be serialized for the queue. An exception will be thrown if you try.
