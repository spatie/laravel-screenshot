---
title: Installation & setup
weight: 4
---

You can install the package via composer:

```bash
composer require spatie/laravel-screenshot
```

## Choosing a driver

This package supports multiple screenshot drivers. You can set the driver via the `LARAVEL_SCREENSHOT_DRIVER` environment variable, or in the config file.

### Browsershot driver (default)

The Browsershot driver requires the `spatie/browsershot` package:

```bash
composer require spatie/browsershot
```

You'll also need to install the required dependencies for Browsershot to work. You can find the instructions [here](https://spatie.be/docs/browsershot/v4/requirements).

### Cloudflare driver

The Cloudflare driver uses [Cloudflare's Browser Rendering API](https://developers.cloudflare.com/browser-rendering/) to take screenshots. It does not require Node.js or a Chrome binary, making it a great choice for cloud-hosted Laravel apps.

To get started with Cloudflare:

1. Make sure you have a [Cloudflare account](https://dash.cloudflare.com/sign-up)
2. In the Cloudflare dashboard, go to **Manage account > Account API tokens** in the sidebar
3. Click **Create Token** and create a token with the **Account.Browser Rendering** permission
4. Your Account ID can be found in the address bar of the Cloudflare dashboard URL
5. Add the following to your `.env` file:

```env
LARAVEL_SCREENSHOT_DRIVER=cloudflare
CLOUDFLARE_API_TOKEN=your-api-token
CLOUDFLARE_ACCOUNT_ID=your-account-id
```

That's it. No other dependencies are required since the Cloudflare driver uses Laravel's built-in HTTP client.

## Publishing the config file

Optionally, you can publish the config file with:

```bash
php artisan vendor:publish --tag=screenshot-config
```

This is the content of the published config file:

```php
return [
    'driver' => env('LARAVEL_SCREENSHOT_DRIVER', 'browsershot'),

    'job' => Spatie\LaravelScreenshot\Jobs\GenerateScreenshotJob::class,

    'defaults' => [
        'width' => 1280,
        'height' => 800,
        'device_scale_factor' => 2,
        'type' => 'png',
        'wait_until' => 'networkidle2',
    ],

    'browsershot' => [
        'node_binary' => env('LARAVEL_SCREENSHOT_NODE_BINARY'),
        'npm_binary' => env('LARAVEL_SCREENSHOT_NPM_BINARY'),
        'include_path' => env('LARAVEL_SCREENSHOT_INCLUDE_PATH'),
        'chrome_path' => env('LARAVEL_SCREENSHOT_CHROME_PATH'),
        'node_modules_path' => env('LARAVEL_SCREENSHOT_NODE_MODULES_PATH'),
        'bin_path' => env('LARAVEL_SCREENSHOT_BIN_PATH'),
        'temp_path' => env('LARAVEL_SCREENSHOT_TEMP_PATH'),
        'write_options_to_file' => env('LARAVEL_SCREENSHOT_WRITE_OPTIONS_TO_FILE', false),
        'no_sandbox' => env('LARAVEL_SCREENSHOT_NO_SANDBOX', false),
    ],

    'cloudflare' => [
        'api_token' => env('CLOUDFLARE_API_TOKEN'),
        'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
    ],
];
```
