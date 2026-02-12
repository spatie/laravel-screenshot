<?php

declare(strict_types=1);

use Spatie\LaravelScreenshot\Facades\Screenshot;
use Spatie\LaravelScreenshot\FakeQueuedScreenshotResponse;
use Spatie\LaravelScreenshot\ScreenshotBuilder;

beforeEach(function () {
    Screenshot::fake();
});

it('can assert a screenshot was queued with a string path', function () {
    Screenshot::url('https://example.com')->saveQueued('queued.png');

    Screenshot::assertQueued('queued.png');
});

it('can assert a screenshot was queued with a callable', function () {
    Screenshot::url('https://example.com')->saveQueued('queued.png');

    Screenshot::assertQueued(function (ScreenshotBuilder $screenshot, string $path) {
        return $path === 'queued.png' && $screenshot->url === 'https://example.com';
    });
});

it('fails when asserting a queued screenshot that was not queued', function () {
    Screenshot::assertQueued('nonexistent.png');
})->fails();

it('fails when asserting a queued screenshot with callable that does not match', function () {
    Screenshot::url('https://example.com')->saveQueued('queued.png');

    Screenshot::assertQueued(function (ScreenshotBuilder $screenshot, string $path) {
        return $path === 'other.png';
    });
})->fails();

it('can assert no screenshots were queued', function () {
    Screenshot::assertNotQueued();
});

it('can assert a specific path was not queued', function () {
    Screenshot::url('https://example.com')->saveQueued('queued.png');

    Screenshot::assertNotQueued('other.png');
});

it('fails assertNotQueued when a screenshot was queued', function () {
    Screenshot::url('https://example.com')->saveQueued('queued.png');

    Screenshot::assertNotQueued();
})->fails();

it('fails assertNotQueued with path when that path was queued', function () {
    Screenshot::url('https://example.com')->saveQueued('queued.png');

    Screenshot::assertNotQueued('queued.png');
})->fails();

it('returns a chainable fake response from saveQueued', function () {
    $response = Screenshot::url('https://example.com')->saveQueued('queued.png');

    expect($response)->toBeInstanceOf(FakeQueuedScreenshotResponse::class);

    $chained = $response
        ->then(fn () => null)
        ->catch(fn () => null)
        ->onQueue('screenshots');

    expect($chained)->toBeInstanceOf(FakeQueuedScreenshotResponse::class);
});
