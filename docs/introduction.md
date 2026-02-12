---
title: Introduction
weight: 1
---

This package provides a simple way to take screenshots of web pages in Laravel apps. It uses a driver-based architecture, so you can choose between different screenshot backends:

- **Browsershot** (default): Uses [Chromium](https://www.chromium.org/chromium-projects/) via [Browsershot](https://spatie.be/docs/browsershot) to take screenshots. Requires Node.js and a Chrome/Chromium binary.
- **Cloudflare**: Uses [Cloudflare's Browser Rendering API](https://developers.cloudflare.com/browser-rendering/) to take screenshots with a simple HTTP call. No Node.js or Chrome binary needed.

Screenshots are taken with beautiful defaults: 1280x800 viewport, 2x device scale factor (retina), PNG format, and waiting for network idle.

Here's a quick example:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')->save('screenshot.png');
```

You can customize the viewport, format, and capture options:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->width(1920)->height(1080)
    ->quality(80)
    ->save('screenshot.jpg');
```

You can queue screenshot generation for background processing:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->saveQueued('screenshot.png')
    ->then(fn (string $path, ?string $diskName) => Mail::to($user)->send(new ScreenshotMail($path)));
```

You can also test your screenshots:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

it('can take a screenshot', function () {
    Screenshot::fake();

    $this->get(route('screenshot'))->assertOk();

    Screenshot::assertSaved(function ($screenshot) {
        return $screenshot->url === 'https://example.com';
    });
});
```

## We got badges

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-screenshot.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-screenshot)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-screenshot/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/spatie/laravel-screenshot/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-screenshot/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/spatie/laravel-screenshot/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-screenshot.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-screenshot)
