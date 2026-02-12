---
title: Saving screenshots to disks
weight: 4
---

Laravel has [a nice filesystem abstraction](https://laravel.com/docs/12.x/filesystem) that allows you to easily save files to any filesystem. It works by configuring a "disk" in `config/filesystems.php` and then using the `Storage` facade to interact with that disk.

Laravel Screenshot can save screenshots to any disk you have configured in your application. To do so, just use the `disk()` method and pass it the name of your configured disk.

Here's an example of saving a screenshot to the `s3` disk:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->disk('s3')
    ->save('screenshots/homepage.png');
```

You can also specify the visibility of the file:

```php
use Spatie\LaravelScreenshot\Facades\Screenshot;

Screenshot::url('https://example.com')
    ->disk('s3', 'public')
    ->save('screenshots/homepage.png');
```
