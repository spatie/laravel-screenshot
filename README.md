<h1>Take screenshots of web pages in Laravel apps</h1>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-screenshot.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-screenshot)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-screenshot/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/spatie/laravel-screenshot/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-screenshot.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-screenshot)

This package provides a simple way to take screenshots of web pages in Laravel apps. It uses a driver-based architecture, so you can choose between [Browsershot](https://spatie.be/docs/browsershot) (Chromium) and [Cloudflare Browser Rendering](https://developers.cloudflare.com/browser-rendering/).

Screenshots are taken with beautiful defaults: 1280x800 viewport, 2x device scale factor (retina), PNG format, and waiting for network idle.

Here's a quick example:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')->save('screenshot.png');
```

You can customize the viewport, format, and capture options:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;
use Spatie\LaravelScreenshot\Enums\ImageType;

Screenshot::url('https://example.com')
    ->width(1920)->height(1080)
    ->type(ImageType::Jpeg)->quality(80)
    ->save('screenshot.jpg');
```

You can also return the screenshot as a response from your controller:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

class ScreenshotController
{
    public function __invoke()
    {
        return Screenshot::url('https://example.com')
            ->name('my-screenshot.png');
    }
}
```

You can test your screenshots too:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

it('can take a screenshot', function () {
    Screenshot::fake();

    $this->get(route('screenshot'))->assertOk();

    Screenshot::assertRespondedWithScreenshot(function ($screenshot) {
        return $screenshot->url === 'https://example.com';
    });
});
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-screenshot.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-screenshot)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Documentation

All documentation is available [on our documentation site](https://spatie.be/docs/laravel-screenshot).

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
