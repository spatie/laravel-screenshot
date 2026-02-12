---
title: Queued screenshot generation
weight: 5
---

Screenshot generation can be slow, especially with the Browsershot or Cloudflare driver. If you don't need the screenshot immediately, you can dispatch the generation to a background queue using `saveQueued()`.

## Basic usage

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->saveQueued('screenshot.png');
```

This will dispatch a queued job that takes the screenshot and saves it to the given path.

## Callbacks

You can chain `then()` and `catch()` callbacks to react to the job's success or failure:

```php
Screenshot::url('https://example.com')
    ->saveQueued('screenshot.png')
    ->then(fn (string $path, ?string $diskName) => Mail::to($user)->send(new ScreenshotMail($path)))
    ->catch(fn (Throwable $e) => Log::error('Screenshot failed', ['error' => $e->getMessage()]));
```

The `then` callback receives the path the screenshot was saved to and the disk name (or `null` for local saves). This makes it easy to retrieve the file afterwards:

```php
->then(function (string $path, ?string $diskName) {
    $contents = $diskName
        ? Storage::disk($diskName)->get($path)
        : file_get_contents($path);
})
```

The `catch` callback receives the exception.

## Queue configuration

You can specify the connection and queue name directly:

```php
Screenshot::url('https://example.com')
    ->saveQueued('screenshot.png', connection: 'redis', queue: 'screenshots');
```

Or use chained methods for full control — these are proxied to Laravel's `PendingDispatch`:

```php
Screenshot::url('https://example.com')
    ->saveQueued('screenshot.png')
    ->onQueue('screenshots')
    ->onConnection('redis')
    ->delay(now()->addMinutes(5));
```

## Saving to a disk

When using `disk()`, the queued job will save the screenshot to the specified disk:

```php
Screenshot::url('https://example.com')
    ->disk('s3')
    ->saveQueued('screenshots/homepage.png')
    ->then(function (string $path, ?string $diskName) {
        $url = Storage::disk($diskName)->url($path);
        // ...
    });
```

## Customizing the job

You can replace the job class used by `saveQueued()` in your `config/laravel-screenshot.php`:

```php
'job' => \App\Jobs\GenerateScreenshotJob::class,
```

Your custom class should extend the default job:

```php
namespace App\Jobs;

use Spatie\LaravelScreenshot\Jobs\GenerateScreenshotJob as BaseJob;

class GenerateScreenshotJob extends BaseJob
{
    public int $tries = 3;

    public int $timeout = 120;

    public int $backoff = 30;
}
```

This lets you set defaults like retry attempts, timeouts, or a default queue for all queued screenshot jobs.

## Limitations

`saveQueued()` cannot be used with `withBrowsershot()`. The closure passed to `withBrowsershot()` may capture objects or state that cannot be reliably serialized for the queue. An exception will be thrown if you try.
