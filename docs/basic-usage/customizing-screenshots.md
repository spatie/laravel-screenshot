---
title: Customizing screenshots
weight: 3
---

There are various options to customize the output of your screenshots.

## Viewport size

By default, screenshots use a 1280x800 viewport. You can change this using `width()`, `height()`, or `size()`:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->width(1920)
    ->height(1080)
    ->save('screenshot.png');

// Or use size() as a shorthand:
Screenshot::url('https://example.com')
    ->size(1920, 1080)
    ->save('screenshot.png');
```

## Image format

The image format is automatically determined from the file extension passed to `save()`. Supported formats:

- `.png` — PNG format (default for unknown extensions)
- `.jpg` / `.jpeg` — JPEG format
- `.webp` — WebP format

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')->save('screenshot.jpg'); // saves as JPEG
Screenshot::url('https://example.com')->save('screenshot.webp'); // saves as WebP
Screenshot::url('https://example.com')->save('screenshot.png'); // saves as PNG
```

## Image quality

For JPEG and WebP formats, you can set the quality (0-100):

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->quality(80)
    ->save('screenshot.jpg');
```

## Device scale factor

By default, screenshots use a 2x device scale factor (retina). You can change this:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

// Standard resolution
Screenshot::url('https://example.com')
    ->deviceScaleFactor(1)
    ->save('screenshot.png');

// 3x resolution
Screenshot::url('https://example.com')
    ->deviceScaleFactor(3)
    ->save('screenshot@3x.png');
```

## Full page screenshots

By default, only the viewport is captured. To capture the entire scrollable page:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->fullPage()
    ->save('full-page.png');
```

## Element screenshots

You can screenshot a specific element on the page using a CSS selector:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->selector('.hero-section')
    ->save('hero.png');
```

## Clip region

You can capture a specific region of the page by specifying x, y coordinates and dimensions:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->clip(0, 0, 800, 600)
    ->save('clipped.png');
```

## Transparent background

To capture the page with a transparent background (useful for PNG screenshots of elements):

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->selector('.logo')
    ->omitBackground()
    ->save('logo.png');
```

## Wait strategies

By default, screenshots wait for the network to be idle (`networkidle2`). You can customize this behavior:

### Wait for a specific network state

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->waitUntil('networkidle0')
    ->save('screenshot.png');
```

### Wait for a CSS selector to appear

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->waitForSelector('.content-loaded')
    ->save('screenshot.png');
```

### Wait for a specific duration

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->waitForTimeout(3000) // wait 3 seconds
    ->save('screenshot.png');
```

## Conditional customization

You can conditionally apply options using the `when` and `unless` methods:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->when($needsFullPage, fn ($screenshot) => $screenshot->fullPage())
    ->when($isRetina, fn ($screenshot) => $screenshot->deviceScaleFactor(2))
    ->save('screenshot.png');
```

## Debugging

You can call `dump` or `dd` on the builder to inspect its current state:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->width(1920)
    ->fullPage()
    ->dump() // dumps the builder state and continues
    ->save('screenshot.png');
```
