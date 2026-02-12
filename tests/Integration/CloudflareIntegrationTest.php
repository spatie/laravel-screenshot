<?php

use Dotenv\Dotenv;
use Spatie\LaravelScreenshot\Drivers\CloudflareDriver;
use Spatie\LaravelScreenshot\ScreenshotOptions;

beforeEach(function () {
    if (file_exists(__DIR__.'/../../.env')) {
        $dotenv = Dotenv::createImmutable(__DIR__.'/../..');
        $dotenv->safeLoad();
    }

    $this->apiToken = env('CLOUDFLARE_API_TOKEN');
    $this->accountId = env('CLOUDFLARE_ACCOUNT_ID');

    if (empty($this->apiToken) || empty($this->accountId)) {
        $this->markTestSkipped('Cloudflare credentials not configured in .env file.');
    }

    $this->driver = new CloudflareDriver([
        'api_token' => $this->apiToken,
        'account_id' => $this->accountId,
    ]);
});

it('can take a screenshot of a url via cloudflare', function () {
    $path = getTempPath('cloudflare-url.png');

    $this->driver->saveScreenshot(
        'https://example.com',
        false,
        new ScreenshotOptions,
        $path,
    );

    expect($path)->toBeFile();
    expect(mime_content_type($path))->toBe('image/png');
})->group('cloudflare');
