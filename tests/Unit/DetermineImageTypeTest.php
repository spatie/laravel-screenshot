<?php

use Spatie\LaravelScreenshot\Actions\DetermineImageType;
use Spatie\LaravelScreenshot\Enums\ImageType;

it('determines png from .png extension', function () {
    $action = new DetermineImageType;

    expect($action('screenshot.png'))->toBe(ImageType::Png);
});

it('determines jpeg from .jpg extension', function () {
    $action = new DetermineImageType;

    expect($action('photo.jpg'))->toBe(ImageType::Jpeg);
});

it('determines jpeg from .jpeg extension', function () {
    $action = new DetermineImageType;

    expect($action('photo.jpeg'))->toBe(ImageType::Jpeg);
});

it('determines webp from .webp extension', function () {
    $action = new DetermineImageType;

    expect($action('image.webp'))->toBe(ImageType::Webp);
});

it('defaults to png for unknown extensions', function () {
    $action = new DetermineImageType;

    expect($action('file.bmp'))->toBe(ImageType::Png);
});

it('handles paths with directories', function () {
    $action = new DetermineImageType;

    expect($action('/tmp/screenshots/page.jpg'))->toBe(ImageType::Jpeg);
});

it('is case insensitive', function () {
    $action = new DetermineImageType;

    expect($action('screenshot.JPG'))->toBe(ImageType::Jpeg);
    expect($action('screenshot.PNG'))->toBe(ImageType::Png);
    expect($action('screenshot.WebP'))->toBe(ImageType::Webp);
});

it('uses the configured default type for unknown extensions', function () {
    config()->set('laravel-screenshot.defaults.type', 'jpeg');

    $action = new DetermineImageType;

    expect($action('file.bmp'))->toBe(ImageType::Jpeg);
});
