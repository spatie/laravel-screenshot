<?php

use Spatie\LaravelScreenshot\Facades\Screenshot;
use Spatie\LaravelScreenshot\FakeScreenshotBuilder;
use Spatie\LaravelScreenshot\ScreenshotBuilder;

use function Spatie\LaravelScreenshot\Support\screenshot;

test('the `screenshot` function returns a builder instance', function () {
    expect(screenshot())->toBeInstanceOf(ScreenshotBuilder::class);
});

test('the `screenshot` function accepts a url', function () {
    Screenshot::fake();

    $builder = screenshot('https://example.com');

    expect($builder)
        ->toBeInstanceOf(FakeScreenshotBuilder::class)
        ->url->toBe('https://example.com');
});

test('the `screenshot` function respects fakes', function () {
    Screenshot::fake();

    expect(screenshot('https://example.com'))->toBeInstanceOf(FakeScreenshotBuilder::class);
});

test('the `screenshot` function without url returns a blank builder', function () {
    $builder = screenshot();

    expect($builder)
        ->toBeInstanceOf(ScreenshotBuilder::class)
        ->url->toBeNull()
        ->html->toBeNull();
});
