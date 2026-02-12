<?php

use Spatie\LaravelScreenshot\ScreenshotFactory;
use Spatie\LaravelScreenshot\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        ScreenshotFactory::resetDefaultBuilder();
    })
    ->in(__DIR__);
