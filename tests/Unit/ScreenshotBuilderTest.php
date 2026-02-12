<?php

use Spatie\LaravelScreenshot\Exceptions\CouldNotTakeScreenshot;
use Spatie\LaravelScreenshot\ScreenshotBuilder;

it('can set url', function () {
    $builder = new ScreenshotBuilder;

    $builder->url('https://example.com');

    expect($builder->url)->toBe('https://example.com');
    expect($builder->html)->toBeNull();
});

it('can set html', function () {
    $builder = new ScreenshotBuilder;

    $builder->html('<h1>Hello</h1>');

    expect($builder->html)->toBe('<h1>Hello</h1>');
    expect($builder->url)->toBeNull();
});

it('can set width', function () {
    $builder = new ScreenshotBuilder;

    $builder->width(1920);

    expect($builder->width)->toBe(1920);
});

it('can set height', function () {
    $builder = new ScreenshotBuilder;

    $builder->height(1080);

    expect($builder->height)->toBe(1080);
});

it('can set size', function () {
    $builder = new ScreenshotBuilder;

    $builder->size(1920, 1080);

    expect($builder->width)->toBe(1920);
    expect($builder->height)->toBe(1080);
});

it('can set quality', function () {
    $builder = new ScreenshotBuilder;

    $builder->quality(80);

    expect($builder->quality)->toBe(80);
});

it('can set full page', function () {
    $builder = new ScreenshotBuilder;

    $builder->fullPage();

    expect($builder->fullPage)->toBeTrue();
});

it('can set selector', function () {
    $builder = new ScreenshotBuilder;

    $builder->selector('.hero');

    expect($builder->selector)->toBe('.hero');
});

it('can set clip', function () {
    $builder = new ScreenshotBuilder;

    $builder->clip(10, 20, 300, 200);

    expect($builder->clip)->toBe(['x' => 10, 'y' => 20, 'width' => 300, 'height' => 200]);
});

it('can set device scale factor', function () {
    $builder = new ScreenshotBuilder;

    $builder->deviceScaleFactor(3);

    expect($builder->deviceScaleFactor)->toBe(3);
});

it('can set omit background', function () {
    $builder = new ScreenshotBuilder;

    $builder->omitBackground();

    expect($builder->omitBackground)->toBeTrue();
});

it('can set wait for timeout', function () {
    $builder = new ScreenshotBuilder;

    $builder->waitForTimeout(3000);

    expect($builder->waitForTimeout)->toBe(3000);
});

it('can set wait for selector', function () {
    $builder = new ScreenshotBuilder;

    $builder->waitForSelector('.loaded');

    expect($builder->waitForSelector)->toBe('.loaded');
});

it('can set wait until', function () {
    $builder = new ScreenshotBuilder;

    $builder->waitUntil('networkidle0');

    expect($builder->waitUntil)->toBe('networkidle0');
});

it('supports fluent interface', function () {
    $builder = new ScreenshotBuilder;

    $result = $builder
        ->url('https://example.com')
        ->width(1920)
        ->height(1080)
        ->quality(90)
        ->fullPage()
        ->selector('.main')
        ->omitBackground()
        ->deviceScaleFactor(2)
        ->waitForTimeout(1000)
        ->waitForSelector('.loaded')
        ->waitUntil('networkidle0');

    expect($result)->toBeInstanceOf(ScreenshotBuilder::class);
});

it('throws when no input is provided', function () {
    $builder = new ScreenshotBuilder;

    invade($builder)->getInput();
})->throws(CouldNotTakeScreenshot::class, 'No URL or HTML');

it('throws when trying to queue with browsershot closure', function () {
    $builder = new ScreenshotBuilder;

    $builder
        ->url('https://example.com')
        ->withBrowsershot(fn () => null)
        ->saveQueued('test.png');
})->throws(CouldNotTakeScreenshot::class, 'Cannot use saveQueued()');

it('builds options with config defaults', function () {
    config()->set('laravel-screenshot.defaults', [
        'width' => 1280,
        'height' => 800,
        'device_scale_factor' => 2,
        'wait_until' => 'networkidle2',
    ]);

    $builder = new ScreenshotBuilder;
    $builder->url('https://example.com');

    $options = $builder->buildOptions();

    expect($options->width)->toBe(1280);
    expect($options->height)->toBe(800);
    expect($options->deviceScaleFactor)->toBe(2);
    expect($options->waitUntil)->toBe('networkidle2');
});

it('overrides config defaults with explicit values', function () {
    config()->set('laravel-screenshot.defaults', [
        'width' => 1280,
        'height' => 800,
        'device_scale_factor' => 2,
    ]);

    $builder = new ScreenshotBuilder;
    $builder->url('https://example.com')->width(1920)->deviceScaleFactor(3);

    $options = $builder->buildOptions();

    expect($options->width)->toBe(1920);
    expect($options->height)->toBe(800); // from config
    expect($options->deviceScaleFactor)->toBe(3);
});

it('identifies html input correctly', function () {
    $builder = new ScreenshotBuilder;
    $builder->html('<h1>Hello</h1>');

    expect(invade($builder)->isHtml())->toBeTrue();
});

it('identifies url input correctly', function () {
    $builder = new ScreenshotBuilder;
    $builder->url('https://example.com');

    expect(invade($builder)->isHtml())->toBeFalse();
});
