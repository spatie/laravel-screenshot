<?php

use Spatie\LaravelScreenshot\Enums\ImageType;
use Spatie\LaravelScreenshot\Facades\Screenshot;

beforeEach(function () {
    Screenshot::fake();
});

it('can fake saving a screenshot', function () {
    Screenshot::url('https://example.com')->save('test.png');

    Screenshot::assertSaved('test.png');
});

it('can assert the url of a screenshot', function () {
    Screenshot::url('https://example.com')->save('test.png');

    Screenshot::assertUrlIs('https://example.com');
});

it('can assert html contains', function () {
    Screenshot::html('<h1>Hello World</h1>')->save('test.png');

    Screenshot::assertHtmlContains('Hello World');
});

it('can fake a screenshot returned as response', function () {
    $response = Screenshot::url('https://example.com')
        ->width(1920)
        ->toResponse(request());

    Screenshot::assertRespondedWithScreenshot(function ($screenshot) {
        return $screenshot->url === 'https://example.com'
            && $screenshot->width === 1920;
    });
});

it('can assert saved with a callback', function () {
    Screenshot::url('https://example.com')
        ->type(ImageType::Jpeg)
        ->quality(80)
        ->save('screenshots/page.jpg');

    Screenshot::assertSaved(function ($screenshot, $path) {
        return $path === 'screenshots/page.jpg'
            && $screenshot->type === ImageType::Jpeg
            && $screenshot->quality === 80;
    });
});

it('can fake a queued screenshot', function () {
    Screenshot::url('https://example.com')->saveQueued('screenshots/page.png');

    Screenshot::assertQueued('screenshots/page.png');
});

it('can assert not queued', function () {
    Screenshot::assertNotQueued();
});

it('can assert not queued with a specific path', function () {
    Screenshot::url('https://example.com')->saveQueued('screenshots/page.png');

    Screenshot::assertNotQueued('screenshots/other.png');
});

it('can assert queued with a callback', function () {
    Screenshot::url('https://example.com')
        ->fullPage()
        ->saveQueued('screenshots/full.png');

    Screenshot::assertQueued(function ($screenshot, $path) {
        return $path === 'screenshots/full.png'
            && $screenshot->fullPage === true;
    });
});

it('can use all builder methods', function () {
    Screenshot::url('https://example.com')
        ->width(1920)
        ->height(1080)
        ->size(1920, 1080)
        ->type(ImageType::Png)
        ->quality(90)
        ->fullPage()
        ->selector('.main')
        ->clip(0, 0, 800, 600)
        ->omitBackground()
        ->deviceScaleFactor(2)
        ->waitForTimeout(1000)
        ->waitForSelector('.loaded')
        ->waitUntil('networkidle0')
        ->save('test.png');

    Screenshot::assertSaved('test.png');
});

it('can assert url from a queued screenshot', function () {
    Screenshot::url('https://spatie.be')->saveQueued('spatie.png');

    Screenshot::assertUrlIs('https://spatie.be');
});

it('can assert url from a response screenshot', function () {
    Screenshot::url('https://spatie.be')->toResponse(request());

    Screenshot::assertUrlIs('https://spatie.be');
});
