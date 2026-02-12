<?php

namespace Spatie\LaravelScreenshot\Exceptions;

use Exception;

class CouldNotTakeScreenshot extends Exception
{
    public static function browsershotNotInstalled(): self
    {
        return new self('The spatie/browsershot package is required to use the Browsershot driver. Install it with: composer require spatie/browsershot');
    }

    public static function missingCloudflareCredentials(): self
    {
        return new self('The Cloudflare driver requires both an API token and account ID. Set CLOUDFLARE_API_TOKEN and CLOUDFLARE_ACCOUNT_ID in your .env file.');
    }

    public static function cloudflareApiError(string $body): self
    {
        return new self("Cloudflare screenshot generation failed: {$body}");
    }

    public static function cannotQueueWithBrowsershotClosure(): self
    {
        return new self('Cannot use saveQueued() with withBrowsershot(). Closures passed to withBrowsershot() cannot be serialized for the queue.');
    }

    public static function noInputProvided(): self
    {
        return new self('No URL or HTML was provided. Use url() or html() to specify the content to screenshot.');
    }
}
