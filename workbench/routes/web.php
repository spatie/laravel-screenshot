<?php

use Illuminate\Support\Facades\Route;
use Spatie\LaravelScreenshot\Facades\Screenshot;

Route::get('screenshot/inline', function () {
    return Screenshot::url('https://example.com')->inline('my-screenshot.png');
});

Route::get('screenshot/download', function () {
    return Screenshot::url('https://example.com')->download('my-screenshot.png');
});

Route::get('screenshot/download-nameless', function () {
    return Screenshot::url('https://example.com')->download();
});

Route::get('screenshot/default', function () {
    return Screenshot::url('https://example.com')->name('my-screenshot.png');
});
