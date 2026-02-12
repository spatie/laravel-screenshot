---
title: Using the Cloudflare driver
weight: 3
---

The Cloudflare driver uses [Cloudflare's Browser Rendering API](https://developers.cloudflare.com/browser-rendering/) to take screenshots. It does not require Node.js or a Chrome binary on your server, making it a great option for cloud-hosted or serverless Laravel applications.

## Setup

1. Make sure you have a [Cloudflare account](https://dash.cloudflare.com/sign-up)
2. In the Cloudflare dashboard, go to **Manage account > Account API tokens**
3. Create a token with the **Account.Browser Rendering** permission
4. Add the following to your `.env` file:

```env
LARAVEL_SCREENSHOT_DRIVER=cloudflare
CLOUDFLARE_API_TOKEN=your-api-token
CLOUDFLARE_ACCOUNT_ID=your-account-id
```

No other dependencies are required. The Cloudflare driver uses Laravel's built-in HTTP client.

## Supported features

The Cloudflare driver supports all the core screenshot features:

- URL and HTML input
- Viewport size (width, height)
- Device scale factor
- Image format (PNG, JPEG, WebP) and quality
- Full page screenshots
- Element screenshots via CSS selector
- Clip region
- Transparent background
- Wait strategies (network idle, selector, timeout)

## Per-screenshot usage

You can use the Cloudflare driver for a specific screenshot while keeping Browsershot as the default:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->driver('cloudflare')
    ->save('screenshot.png');
```

## Rate limits

Be aware of Cloudflare's rate limits for the Browser Rendering API. The free plan includes:

- 6 REST API requests per minute
- 10 minutes of Browser Rendering per day

For production use, consider a paid plan with higher limits.

## Limitations

- The `withBrowsershot()` method has no effect when using the Cloudflare driver
- Cloudflare's API may not support all Chrome-specific options available through Browsershot
