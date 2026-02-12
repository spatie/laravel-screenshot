---
title: Extending with macros
weight: 1
---

The `ScreenshotBuilder` class uses Laravel's `Macroable` trait, which means you can add custom methods to it.

## Registering macros

You can register macros in the `boot` method of a service provider:

```php
use Spatie\LaravelScreenshot\ScreenshotBuilder;

// in a service provider

public function boot(): void
{
    ScreenshotBuilder::macro('mobile', function () {
        return $this
            ->size(375, 812)
            ->deviceScaleFactor(3);
    });

    ScreenshotBuilder::macro('desktop', function () {
        return $this
            ->size(1920, 1080)
            ->deviceScaleFactor(2);
    });
}
```

## Using macros

Once registered, you can use your macros like any other builder method:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->mobile()
    ->save('mobile.png');

Screenshot::url('https://example.com')
    ->desktop()
    ->save('desktop.png');
```

This is useful for defining common screenshot configurations that you use across your application.
