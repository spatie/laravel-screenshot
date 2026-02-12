---
title: Taking screenshots
weight: 1
---

This package can take screenshots of any URL or raw HTML.

## Screenshotting a URL

The most common use case is taking a screenshot of a URL:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')->save('screenshot.png');
```

## Screenshotting HTML

You can also take a screenshot from a string of HTML:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::html('<h1>Hello world!</h1>')->save('hello.png');
```

## Using JavaScript

The JavaScript in your HTML will be executed when the screenshot is taken. You could use this to have a JavaScript charting library render a chart.

Here's a simple example. If you screenshot this HTML...

```html
<div id="target"></div>

<script>
    document.getElementById('target').innerHTML = 'hello';
</script>
```

... using this code...

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::html($html)->save('screenshot.png');
```

... the text `hello` will be visible in the screenshot.
