<?php

declare(strict_types=1);

use Spatie\LaravelScreenshot\Drivers\CloudflareDriver;
use Spatie\LaravelScreenshot\Enums\ImageType;
use Spatie\LaravelScreenshot\Exceptions\CouldNotTakeScreenshot;
use Spatie\LaravelScreenshot\ScreenshotOptions;

it('throws when credentials are missing', function () {
    new CloudflareDriver([]);
})->throws(CouldNotTakeScreenshot::class, 'API token and account ID');

it('throws when api token is empty', function () {
    new CloudflareDriver(['api_token' => '', 'account_id' => 'test']);
})->throws(CouldNotTakeScreenshot::class);

it('throws when account id is empty', function () {
    new CloudflareDriver(['api_token' => 'test', 'account_id' => '']);
})->throws(CouldNotTakeScreenshot::class);

it('builds request body for url input', function () {
    $driver = new CloudflareDriver(['api_token' => 'test', 'account_id' => 'test']);

    $body = invade($driver)->buildRequestBody('https://example.com', false, new ScreenshotOptions);

    expect($body)->toHaveKey('url', 'https://example.com');
    expect($body)->not->toHaveKey('html');
});

it('builds request body for html input', function () {
    $driver = new CloudflareDriver(['api_token' => 'test', 'account_id' => 'test']);

    $body = invade($driver)->buildRequestBody('<h1>Hello</h1>', true, new ScreenshotOptions);

    expect($body)->toHaveKey('html', '<h1>Hello</h1>');
    expect($body)->not->toHaveKey('url');
});

it('includes viewport in request body', function () {
    $driver = new CloudflareDriver(['api_token' => 'test', 'account_id' => 'test']);

    $options = new ScreenshotOptions(width: 1920, height: 1080, deviceScaleFactor: 2);
    $body = invade($driver)->buildRequestBody('https://example.com', false, $options);

    expect($body['viewport'])->toBe([
        'width' => 1920,
        'height' => 1080,
        'deviceScaleFactor' => 2,
    ]);
});

it('includes screenshot options in request body', function () {
    $driver = new CloudflareDriver(['api_token' => 'test', 'account_id' => 'test']);

    $options = new ScreenshotOptions(
        type: ImageType::Jpeg,
        quality: 85,
        fullPage: true,
        omitBackground: true,
    );
    $body = invade($driver)->buildRequestBody('https://example.com', false, $options);

    expect($body['screenshotOptions'])->toBe([
        'type' => 'jpeg',
        'quality' => 85,
        'fullPage' => true,
        'omitBackground' => true,
    ]);
});

it('includes selector in screenshot options', function () {
    $driver = new CloudflareDriver(['api_token' => 'test', 'account_id' => 'test']);

    $options = new ScreenshotOptions(selector: '.hero');
    $body = invade($driver)->buildRequestBody('https://example.com', false, $options);

    expect($body['screenshotOptions']['selector'])->toBe('.hero');
});

it('includes clip in screenshot options', function () {
    $driver = new CloudflareDriver(['api_token' => 'test', 'account_id' => 'test']);

    $options = new ScreenshotOptions(clip: ['x' => 0, 'y' => 0, 'width' => 800, 'height' => 600]);
    $body = invade($driver)->buildRequestBody('https://example.com', false, $options);

    expect($body['screenshotOptions']['clip'])->toBe(['x' => 0, 'y' => 0, 'width' => 800, 'height' => 600]);
});

it('includes wait options in request body', function () {
    $driver = new CloudflareDriver(['api_token' => 'test', 'account_id' => 'test']);

    $options = new ScreenshotOptions(
        waitUntil: 'networkidle0',
        waitForSelector: '.loaded',
        waitForTimeout: 3000,
    );
    $body = invade($driver)->buildRequestBody('https://example.com', false, $options);

    expect($body['gotoOptions'])->toBe(['waitUntil' => 'networkidle0']);
    expect($body['waitForSelector'])->toBe(['selector' => '.loaded']);
    expect($body['waitForTimeout'])->toBe(3000);
});

it('does not include empty screenshot options', function () {
    $driver = new CloudflareDriver(['api_token' => 'test', 'account_id' => 'test']);

    $body = invade($driver)->buildRequestBody('https://example.com', false, new ScreenshotOptions);

    expect($body)->not->toHaveKey('viewport');
    expect($body)->not->toHaveKey('gotoOptions');
    expect($body)->not->toHaveKey('waitForSelector');
    expect($body)->not->toHaveKey('waitForTimeout');
});
