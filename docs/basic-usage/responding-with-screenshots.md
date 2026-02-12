---
title: Responding with screenshots
weight: 2
---

In a controller, you can create and return a screenshot as a response:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

class ScreenshotController
{
    public function __invoke()
    {
        return Screenshot::url('https://example.com')
            ->name('my-screenshot.png');
    }
}
```

By default, the screenshot will be inlined in the browser. This means that the image will be displayed in the browser directly. We recommend that you always name your screenshots.

If you want to force the screenshot to be downloaded, you can use the `download()` method:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

class ScreenshotController
{
    public function __invoke()
    {
        return Screenshot::url('https://example.com')
            ->download('my-screenshot.png');
    }
}
```

You can also explicitly inline the screenshot:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

class ScreenshotController
{
    public function __invoke()
    {
        return Screenshot::url('https://example.com')
            ->inline('my-screenshot.png');
    }
}
```

## Getting the base64 representation

If you need the screenshot as a base64 string (for example, to embed it in an email), you can use the `base64()` method:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

$base64 = Screenshot::url('https://example.com')->base64();
```
