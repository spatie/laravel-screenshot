---
name: laravel-screenshot
description: Take screenshots of web pages in Laravel using spatie/laravel-screenshot. Covers taking screenshots of URLs and HTML, customizing viewport and format, saving to disks, queued generation, testing, and configuring drivers (Browsershot, Cloudflare).
---

# Laravel Screenshot

## When to use this skill

Use this skill when the user needs to take screenshots of web pages in a Laravel application using `spatie/laravel-screenshot`. This includes taking screenshots of URLs or HTML, customizing viewport size and image format, saving to filesystem disks, queued screenshot generation, testing screenshots, and configuring drivers.

## Taking screenshots

Screenshot a URL:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')->save('screenshot.png');
```

Screenshot raw HTML:

```php
Screenshot::html('<h1>Hello world!</h1>')->save('hello.png');
```

## Customizing screenshots

### Viewport size

```php
Screenshot::url('https://example.com')
    ->width(1920)->height(1080)
    ->save('screenshot.png');

// Or use size() as shorthand:
Screenshot::url('https://example.com')
    ->size(1920, 1080)
    ->save('screenshot.png');
```

### Image format

The format is determined from the file extension: `.png`, `.jpg`/`.jpeg`, `.webp`.

```php
Screenshot::url('https://example.com')->save('screenshot.jpg');
Screenshot::url('https://example.com')->save('screenshot.webp');
```

### Image quality

For JPEG and WebP (0-100):

```php
Screenshot::url('https://example.com')
    ->quality(80)
    ->save('screenshot.jpg');
```

### Device scale factor

Default is 2x (retina):

```php
Screenshot::url('https://example.com')
    ->deviceScaleFactor(1)
    ->save('screenshot.png');
```

### Full page screenshots

```php
Screenshot::url('https://example.com')
    ->fullPage()
    ->save('full-page.png');
```

### Element screenshots

```php
Screenshot::url('https://example.com')
    ->selector('.hero-section')
    ->save('hero.png');
```

### Clip region

```php
Screenshot::url('https://example.com')
    ->clip(0, 0, 800, 600)
    ->save('clipped.png');
```

### Transparent background

```php
Screenshot::url('https://example.com')
    ->selector('.logo')
    ->omitBackground()
    ->save('logo.png');
```

### Wait strategies

```php
// Wait for network idle
Screenshot::url('https://example.com')
    ->waitUntil('networkidle0')
    ->save('screenshot.png');

// Wait for a CSS selector
Screenshot::url('https://example.com')
    ->waitForSelector('.content-loaded')
    ->save('screenshot.png');

// Wait for a duration (ms)
Screenshot::url('https://example.com')
    ->waitForTimeout(3000)
    ->save('screenshot.png');
```

### Conditional customization

```php
Screenshot::url('https://example.com')
    ->when($needsFullPage, fn ($screenshot) => $screenshot->fullPage())
    ->save('screenshot.png');
```

## Saving to disks

```php
Screenshot::url('https://example.com')
    ->disk('s3')
    ->save('screenshots/homepage.png');

// With visibility:
Screenshot::url('https://example.com')
    ->disk('s3', 'public')
    ->save('screenshots/homepage.png');
```

## Base64

```php
$base64 = Screenshot::url('https://example.com')->base64();
```

## Drivers

The package supports two drivers: `browsershot` (default) and `cloudflare`.

Set the driver via `LARAVEL_SCREENSHOT_DRIVER` env variable or in `config/laravel-screenshot.php`.

### Browsershot driver

Requires `spatie/browsershot` installed separately:

```bash
composer require spatie/browsershot
```

Customize the Browsershot instance per screenshot:

```php
use Spatie\Browsershot\Browsershot;

Screenshot::url('https://example.com')
    ->withBrowsershot(function (Browsershot $browsershot) {
        $browsershot
            ->setExtraHttpHeaders(['Authorization' => 'Bearer token'])
            ->userAgent('My Custom User Agent');
    })
    ->save('screenshot.png');
```

### Cloudflare driver

Uses Cloudflare's Browser Rendering API. No Node.js or Chrome binary needed.

```env
LARAVEL_SCREENSHOT_DRIVER=cloudflare
CLOUDFLARE_API_TOKEN=your-api-token
CLOUDFLARE_ACCOUNT_ID=your-account-id
```

The Cloudflare driver does not support `withBrowsershot()`.

Switch driver per screenshot:

```php
Screenshot::url('https://example.com')
    ->driver('cloudflare')
    ->save('screenshot.png');
```

## Queued screenshot generation

Dispatch screenshot generation to a background queue:

```php
Screenshot::url('https://example.com')
    ->saveQueued('screenshot.png')
    ->then(fn (string $path, ?string $diskName) => Mail::to($user)->send(new ScreenshotMail($path)))
    ->catch(fn (Throwable $e) => Log::error($e->getMessage()));
```

Configure queue and connection:

```php
Screenshot::url('https://example.com')
    ->saveQueued('screenshot.png')
    ->onQueue('screenshots')
    ->onConnection('redis')
    ->delay(now()->addMinutes(5));
```

With disk:

```php
Screenshot::url('https://example.com')
    ->disk('s3')
    ->saveQueued('screenshots/homepage.png');
```

Note: `saveQueued()` cannot be used with `withBrowsershot()`.

## Extending with macros

Register macros in a service provider:

```php
use Spatie\LaravelScreenshot\ScreenshotBuilder;

ScreenshotBuilder::macro('mobile', function () {
    return $this->size(375, 812)->deviceScaleFactor(3);
});
```

Then use them:

```php
Screenshot::url('https://example.com')->mobile()->save('mobile.png');
```

## Testing

Fake screenshot generation in tests:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

beforeEach(function () {
    Screenshot::fake();
});
```

Assert a screenshot was saved:

```php
Screenshot::assertSaved('screenshots/homepage.png');

Screenshot::assertSaved(function ($screenshot, string $path) {
    return $path === 'screenshots/homepage.png'
        && $screenshot->url === 'https://example.com';
});
```

Assert URL or HTML:

```php
Screenshot::assertUrlIs('https://example.com');
Screenshot::assertHtmlContains('Hello World');
```

Assert queued screenshots:

```php
Screenshot::assertQueued('screenshots/homepage.png');
Screenshot::assertQueued(fn ($screenshot, string $path) => $path === 'screenshots/homepage.png');
Screenshot::assertNotQueued();
```
