---
title: Setting defaults
weight: 7
---

You can set the default options for every screenshot by using the `default` method on the `Screenshot` facade.

Typically, you would do this in the `boot` method of a service provider.

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

// in a service provider

Screenshot::default()
    ->width(1920)
    ->deviceScaleFactor(2);
```

With this code, every screenshot taken in your app will use a 1920px wide viewport with a 2x device scale factor.

Of course, you can still override these defaults when taking a screenshot:

```php
// this screenshot will use the defaults: 1920px wide, 2x retina

Screenshot::url('https://example.com')
    ->save('default.png');

// here we override the default: this screenshot will be 800px wide

Screenshot::url('https://example.com')
    ->width(800)
    ->save('small.png');
```

Note that the package already ships with sensible defaults configured in the config file: 1280x800 viewport, 2x device scale factor, PNG format, and `networkidle2` wait strategy. The `default()` method lets you override these programmatically.
