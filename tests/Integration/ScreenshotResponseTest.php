<?php

declare(strict_types=1);

use Spatie\LaravelScreenshot\Facades\Screenshot;

it('can inline a screenshot', function () {
    Screenshot::fake();

    $this
        ->get('screenshot/inline')
        ->assertHeader('content-type', 'image/png')
        ->assertHeader('content-disposition', 'inline; filename="my-screenshot.png"');
});

it('can download a screenshot with a name', function () {
    Screenshot::fake();

    $this
        ->get('screenshot/download')
        ->assertHeader('content-type', 'image/png')
        ->assertHeader('content-disposition', 'attachment; filename="my-screenshot.png"');
});

it('can download a screenshot without a name', function () {
    Screenshot::fake();

    $this
        ->get('screenshot/download-nameless')
        ->assertHeader('content-type', 'image/png');
});

it('will inline the screenshot by default', function () {
    Screenshot::fake();

    $this
        ->get('screenshot/default')
        ->assertHeader('content-type', 'image/png');
});
