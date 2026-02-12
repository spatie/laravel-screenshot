<?php


use Spatie\LaravelScreenshot\Drivers\BrowsershotDriver;
use Spatie\LaravelScreenshot\Enums\ImageType;
use Spatie\LaravelScreenshot\ScreenshotOptions;

it('creates a browsershot instance for url input', function () {
    $driver = new BrowsershotDriver;
    $browsershot = invade($driver)->buildBrowsershot('https://example.com', false, new ScreenshotOptions);

    expect(invade($browsershot)->url)->toBe('https://example.com');
});

it('creates a browsershot instance for html input', function () {
    $driver = new BrowsershotDriver;
    $browsershot = invade($driver)->buildBrowsershot('<h1>Hello</h1>', true, new ScreenshotOptions);

    // HTML input creates a temporary file, but url should be empty
    expect(invade($browsershot)->url)->toBeEmpty();
});

it('applies viewport size', function () {
    $driver = new BrowsershotDriver;

    $options = new ScreenshotOptions(width: 1920, height: 1080);
    $browsershot = invade($driver)->buildBrowsershot('https://example.com', false, $options);

    expect(getBrowsershotDriverOption($browsershot, 'viewport.width'))->toBe(1920);
    expect(getBrowsershotDriverOption($browsershot, 'viewport.height'))->toBe(1080);
});

it('applies device scale factor', function () {
    $driver = new BrowsershotDriver;

    $options = new ScreenshotOptions(deviceScaleFactor: 3);
    $browsershot = invade($driver)->buildBrowsershot('https://example.com', false, $options);

    expect(getBrowsershotDriverOption($browsershot, 'viewport.deviceScaleFactor'))->toBe(3);
});

it('applies screenshot type', function () {
    $driver = new BrowsershotDriver;

    $options = new ScreenshotOptions(type: ImageType::Jpeg, quality: 85);
    $browsershot = invade($driver)->buildBrowsershot('https://example.com', false, $options);

    expect(invade($browsershot)->screenshotType)->toBe('jpeg');
    expect(invade($browsershot)->screenshotQuality)->toBe(85);
});

it('applies full page option', function () {
    $driver = new BrowsershotDriver;

    $options = new ScreenshotOptions(fullPage: true);
    $browsershot = invade($driver)->buildBrowsershot('https://example.com', false, $options);

    expect(getBrowsershotDriverOption($browsershot, 'fullPage'))->toBeTrue();
});

it('applies clip region', function () {
    $driver = new BrowsershotDriver;

    $options = new ScreenshotOptions(clip: ['x' => 10, 'y' => 20, 'width' => 300, 'height' => 200]);
    $browsershot = invade($driver)->buildBrowsershot('https://example.com', false, $options);

    $clip = getBrowsershotDriverOption($browsershot, 'clip');
    expect($clip)->toBe(['x' => 10, 'y' => 20, 'width' => 300, 'height' => 200]);
});

it('applies omit background', function () {
    $driver = new BrowsershotDriver;

    $options = new ScreenshotOptions(omitBackground: true);
    $browsershot = invade($driver)->buildBrowsershot('https://example.com', false, $options);

    expect(invade($browsershot)->showBackground)->toBeFalse();
});

it('applies wait for timeout as delay', function () {
    $driver = new BrowsershotDriver;

    $options = new ScreenshotOptions(waitForTimeout: 3000);
    $browsershot = invade($driver)->buildBrowsershot('https://example.com', false, $options);

    expect(getBrowsershotDriverOption($browsershot, 'delay'))->toBe(3000);
});

it('applies wait until networkidle0', function () {
    $driver = new BrowsershotDriver;

    $options = new ScreenshotOptions(waitUntil: 'networkidle0');
    $browsershot = invade($driver)->buildBrowsershot('https://example.com', false, $options);

    expect(getBrowsershotDriverOption($browsershot, 'waitUntil'))->toBe('networkidle0');
});

it('applies wait until networkidle2', function () {
    $driver = new BrowsershotDriver;

    $options = new ScreenshotOptions(waitUntil: 'networkidle2');
    $browsershot = invade($driver)->buildBrowsershot('https://example.com', false, $options);

    expect(getBrowsershotDriverOption($browsershot, 'waitUntil'))->toBe('networkidle2');
});

function getBrowsershotDriverOption(object $browsershot, string $key): mixed
{
    $options = invade($browsershot)->additionalOptions;

    return data_get($options, $key);
}
