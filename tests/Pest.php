<?php

use Spatie\LaravelScreenshot\ScreenshotFactory;
use Spatie\LaravelScreenshot\Tests\TestCase;
use Spatie\TemporaryDirectory\TemporaryDirectory;

uses(TestCase::class)
    ->beforeAll(function () {
        (new TemporaryDirectory(getTempPath()))->delete();
    })
    ->beforeEach(function () {
        ScreenshotFactory::resetDefaultBuilder();

        $this
            ->tempDir = (new TemporaryDirectory(getTestSupportPath()))
            ->name('temp')
            ->force()
            ->create();
    })
    ->in(__DIR__);

function getTestSupportPath($suffix = ''): string
{
    return __DIR__."/TestSupport/{$suffix}";
}

function getTempPath($suffix = ''): string
{
    return getTestSupportPath('temp/'.$suffix);
}
