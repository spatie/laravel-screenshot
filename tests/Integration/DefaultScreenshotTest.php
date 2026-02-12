<?php


use Spatie\LaravelScreenshot\Facades\Screenshot;
use Spatie\LaravelScreenshot\ScreenshotFactory;

it('can set defaults for screenshots', function () {
    Screenshot::default()->width(1920);

    $firstPath = getTempPath('first.png');
    Screenshot::html('<h1>Hello</h1>')->save($firstPath);
    expect($firstPath)->toBeFile();

    $firstSize = getimagesize($firstPath);
    // Default device_scale_factor is 2, so 1920 * 2 = 3840
    expect($firstSize[0])->toBe(3840);

    $secondPath = getTempPath('second.png');
    Screenshot::html('<h1>Hello</h1>')->save($secondPath);
    expect($secondPath)->toBeFile();

    $secondSize = getimagesize($secondPath);
    expect($secondSize[0])->toBe(3840);
});

it('will not use properties of the previous screenshot when not setting a default', function () {
    $firstPath = getTempPath('first.png');
    Screenshot::html('<div style="height:3000px">Tall content</div>')
        ->width(500)
        ->save($firstPath);

    $secondPath = getTempPath('second.png');
    Screenshot::html('<div style="height:3000px">Tall content</div>')
        ->save($secondPath);

    // The second screenshot should use the default width (1280), not 500
    $firstSize = getimagesize($firstPath);
    $secondSize = getimagesize($secondPath);

    // With 2x DPR: first = 1000px, second = 2560px
    // If properties leaked, second would also be 1000px
    expect($secondSize[0])->toBe($firstSize[0]);
})->fails();

it('preserves defaults after the facade is cleared', function () {
    Screenshot::default()->width(1920);

    $firstPath = getTempPath('first.png');
    Screenshot::html('<h1>Hello</h1>')->save($firstPath);

    $firstSize = getimagesize($firstPath);
    expect($firstSize[0])->toBe(3840);

    // Simulate what happens in queue:work when the container is flushed
    Screenshot::clearResolvedInstance(ScreenshotFactory::class);

    $secondPath = getTempPath('second.png');
    Screenshot::html('<h1>Hello</h1>')->save($secondPath);

    $secondSize = getimagesize($secondPath);
    expect($secondSize[0])->toBe(3840);
});
