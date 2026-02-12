---
title: Requirements
weight: 3
---

The laravel-screenshot package requires **PHP 8.4+** and **Laravel 12+**.

The additional requirements depend on which driver you use:

## Browsershot driver (default)

The Browsershot driver uses [Browsershot](https://spatie.be/docs/browsershot) under the hood to take screenshots. You can find the necessary requirements [here](https://spatie.be/docs/browsershot/v4/requirements). This includes Node.js and a Chrome/Chromium binary.

## Cloudflare driver

The Cloudflare driver uses [Cloudflare's Browser Rendering API](https://developers.cloudflare.com/browser-rendering/). This driver does not require Node.js or a Chrome binary on your server. You will need:

- A [Cloudflare account](https://dash.cloudflare.com/sign-up) with the Browser Rendering API enabled
- A Cloudflare API token with the appropriate permissions
- Your Cloudflare account ID
