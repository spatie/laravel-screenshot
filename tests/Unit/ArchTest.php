<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('will use strict types in source files')
    ->expect('Spatie\LaravelScreenshot')
    ->not->toUse('Spatie\LaravelScreenshot\Support')
    ->toUseStrictTypes();
