<?php

use Illuminate\Support\Facades\Bus;
use Spatie\LaravelScreenshot\Exceptions\CouldNotTakeScreenshot;
use Spatie\LaravelScreenshot\Facades\Screenshot;
use Spatie\LaravelScreenshot\Jobs\GenerateScreenshotJob;
use Spatie\LaravelScreenshot\QueuedScreenshotResponse;

class CustomGenerateScreenshotJob extends GenerateScreenshotJob
{
    public int $tries = 3;

    public int $timeout = 120;
}

it('dispatches a job when calling saveQueued', function () {
    Bus::fake();

    Screenshot::url('https://example.com')->saveQueued('test.png');

    Bus::assertDispatched(GenerateScreenshotJob::class);
});

it('returns a QueuedScreenshotResponse', function () {
    Bus::fake();

    $response = Screenshot::url('https://example.com')->saveQueued('test.png');

    expect($response)->toBeInstanceOf(QueuedScreenshotResponse::class);
});

it('throws when withBrowsershot is used with saveQueued', function () {
    Screenshot::url('https://example.com')
        ->withBrowsershot(function () {})
        ->saveQueued('test.png');
})->throws(CouldNotTakeScreenshot::class, 'Cannot use saveQueued() with withBrowsershot()');

it('uses a custom job class from config', function () {
    Bus::fake();

    config()->set('laravel-screenshot.job', CustomGenerateScreenshotJob::class);

    Screenshot::url('https://example.com')->saveQueued('test.png');

    Bus::assertDispatched(CustomGenerateScreenshotJob::class);
});

it('generates a screenshot when the job runs synchronously', function () {
    $path = getTempPath('queued-sync.png');

    $job = new GenerateScreenshotJob(
        input: '<h1>Queued sync test</h1>',
        isHtml: true,
        options: new \Spatie\LaravelScreenshot\ScreenshotOptions,
        path: $path,
    );

    dispatch_sync($job);

    expect(file_exists($path))->toBeTrue();
    expect(mime_content_type($path))->toBe('image/png');
});
