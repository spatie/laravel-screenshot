<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('will not use strict types in source files')
    ->expect('Spatie\LaravelScreenshot')
    ->not->toUseStrictTypes();
