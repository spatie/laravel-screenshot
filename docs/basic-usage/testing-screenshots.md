---
title: Testing screenshots
weight: 6
---

In your test, you can call the `fake()` method on the `Screenshot` facade to fake the screenshot generation. Because the screenshot generation is faked, your tests will run much faster.

```php
// in your test

use Spatie\LaravelScreenshot\Facades\Screenshot;

beforeEach(function () {
    Screenshot::fake();
});
```

## assertSaved

You can use the `assertSaved` method to assert that a screenshot was saved. You can pass a string path or a callable.

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::assertSaved('screenshots/homepage.png');
```

With a callable for more detailed assertions:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::assertSaved(function ($screenshot, string $path) {
    return $path === 'screenshots/homepage.png'
        && $screenshot->url === 'https://example.com';
});
```

## assertRespondedWithScreenshot

The `assertRespondedWithScreenshot` method can be used to assert that a screenshot was generated and returned as a response.

Imagine you have this route:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Route::get('take-screenshot', function () {
    return Screenshot::url('https://example.com')
        ->download('homepage.png');
});
```

In your test for this route you can use `assertRespondedWithScreenshot` to make sure that a screenshot was generated and returned:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

it('can download a screenshot', function () {
    Screenshot::fake();

    $this
        ->get('take-screenshot')
        ->assertOk();

    Screenshot::assertRespondedWithScreenshot(function ($screenshot) {
        return $screenshot->url === 'https://example.com';
    });
});
```

## assertUrlIs

You can use the `assertUrlIs` method to assert that a screenshot was taken of a specific URL:

```php
Screenshot::assertUrlIs('https://example.com');
```

## assertHtmlContains

You can use the `assertHtmlContains` method to assert that a screenshot was taken from HTML containing a given string:

```php
Screenshot::assertHtmlContains('Hello World');
```

## Queued screenshot assertions

### assertQueued

You can use the `assertQueued` method to assert that a screenshot was queued for generation. You can pass a string path or a callable.

```php
Screenshot::assertQueued('screenshots/homepage.png');
```

With a callable for more detailed assertions:

```php
Screenshot::assertQueued(function ($screenshot, string $path) {
    return $path === 'screenshots/homepage.png'
        && $screenshot->fullPage === true;
});
```

### assertNotQueued

You can use the `assertNotQueued` method to assert that no screenshots were queued, or that a specific path was not queued.

```php
// Assert nothing was queued
Screenshot::assertNotQueued();

// Assert a specific path was not queued
Screenshot::assertNotQueued('screenshots/other.png');
```
