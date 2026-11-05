<?php

use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelScreenshot\Facades\Screenshot;
use Symfony\Component\Process\Process;

$server = new Process([PHP_BINARY, '-S', '127.0.0.1:0', '-t', __DIR__.'/../TestSupport/website']);
$url = '';

beforeAll(function () use ($server, &$url) {
    $server->start();
    $ready = $server->waitUntil(function ($type, $output) use (&$url) {
        if (! preg_match('~Development Server \((http://127\.0\.0\.1:\d+)\) started~', $output, $matches)) {
            return false;
        }

        $url = $matches[1];

        return true;
    });

    if (! $ready) {
        throw new RuntimeException('Fixture HTTP server failed to start: '.$server->getErrorOutput());
    }

    $server->setTimeout(null);
});

afterAll(function () use ($server) {
    $server->stop();
});

beforeEach(function () use (&$url) {
    $this->url = $url;
    $this->targetPath = getTempPath('test.png');
});

it('can take a screenshot of a url', function () {
    Screenshot::url($this->url)->save($this->targetPath);

    expect(file_get_contents($this->url))->toContain('<h1>Screenshot test page</h1>');
    expect($this->targetPath)->toBeFile();
    expect(mime_content_type($this->targetPath))->toBe('image/png');
});

it('can take a screenshot of html', function () {
    Screenshot::html('<h1>Hello World</h1>')->save($this->targetPath);

    expect($this->targetPath)->toBeFile();
    expect(mime_content_type($this->targetPath))->toBe('image/png');
});

it('can take a screenshot as jpeg', function () {
    $path = getTempPath('test.jpg');

    Screenshot::url($this->url)
        ->quality(80)
        ->save($path);

    expect($path)->toBeFile();
    expect(mime_content_type($path))->toBe('image/jpeg');
});

it('can take a full page screenshot', function () {
    Screenshot::url($this->url)
        ->fullPage()
        ->save($this->targetPath);

    expect($this->targetPath)->toBeFile();
    expect(mime_content_type($this->targetPath))->toBe('image/png');
    expect(getimagesize($this->targetPath)[1])->toBe(3600);
});

it('can take a screenshot with custom viewport', function () {
    Screenshot::url($this->url)
        ->size(1920, 1080)
        ->save($this->targetPath);

    expect($this->targetPath)->toBeFile();
});

it('can take a screenshot with device scale factor', function () {
    Screenshot::url($this->url)
        ->width(800)
        ->height(600)
        ->deviceScaleFactor(1)
        ->save($this->targetPath);

    expect($this->targetPath)->toBeFile();
});

it('can take a screenshot with a selector', function () {
    Screenshot::html('<div class="target" style="width: 200px; height: 100px; background: red;">Target</div>')
        ->selector('.target')
        ->save($this->targetPath);

    expect($this->targetPath)->toBeFile();
});

it('can take a screenshot with omitted background', function () {
    Screenshot::html('<div style="width: 100px; height: 100px; background: transparent;">Hi</div>')
        ->omitBackground()
        ->save($this->targetPath);

    expect($this->targetPath)->toBeFile();
});

it('can customize browsershot', function () {
    Screenshot::url($this->url)
        ->withBrowsershot(function (Browsershot $browsershot) {
            $browsershot->userAgent('TestAgent/1.0');
        })
        ->save($this->targetPath);

    expect($this->targetPath)->toBeFile();
});

it('can get a base64 encoded screenshot', function () {
    $base64 = Screenshot::url($this->url)->base64();

    expect($base64)->toBeString();
    expect(base64_decode($base64, true))->not->toBeFalse();
});

it('can save a screenshot to a disk', function () {
    Storage::fake('local');

    Screenshot::url($this->url)
        ->disk('local')
        ->save('screenshot.png');

    Storage::disk('local')->assertExists('screenshot.png');
});

it('will use a fresh instance after saving', function () {
    Screenshot::url($this->url)
        ->width(1920)
        ->save(getTempPath('first.png'));

    Screenshot::url($this->url)
        ->width(800)
        ->save(getTempPath('second.png'));

    expect(getTempPath('first.png'))->toBeFile();
    expect(getTempPath('second.png'))->toBeFile();
});

it('will execute javascript in html screenshots', function () {
    $html = <<<'HTML'
    <div id="target"></div>
    <script>
        document.getElementById('target').innerHTML = '<div style="width:100px;height:100px;background:blue;">JS rendered</div>';
    </script>
    HTML;

    Screenshot::html($html)->save($this->targetPath);

    expect($this->targetPath)->toBeFile();
});

it('can wait for a timeout before taking screenshot', function () {
    Screenshot::url($this->url)
        ->waitForTimeout(500)
        ->save($this->targetPath);

    expect($this->targetPath)->toBeFile();
});
