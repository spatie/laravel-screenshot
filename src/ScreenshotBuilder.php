<?php


namespace Spatie\LaravelScreenshot;

use Closure;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\Macroable;
use Spatie\LaravelScreenshot\Drivers\BrowsershotDriver;
use Spatie\LaravelScreenshot\Drivers\ScreenshotDriver;
use Spatie\LaravelScreenshot\Enums\ImageType;
use Spatie\LaravelScreenshot\Exceptions\CouldNotTakeScreenshot;
use Spatie\LaravelScreenshot\Exceptions\InvalidDriver;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\HttpFoundation\Response;

class ScreenshotBuilder implements Responsable
{
    use Conditionable;
    use Dumpable;
    use Macroable;

    public ?string $url = null;

    public ?string $html = null;

    public ?string $downloadName = null;

    public ?int $width = null;

    public ?int $height = null;

    public ?ImageType $type = null;

    public ?int $quality = null;

    public ?bool $fullPage = null;

    public ?string $selector = null;

    public ?array $clip = null;

    public ?int $deviceScaleFactor = null;

    public ?bool $omitBackground = null;

    public ?int $waitForTimeout = null;

    public ?string $waitForSelector = null;

    public ?string $waitUntil = null;

    protected ?string $driverName = null;

    protected ?ScreenshotDriver $driver = null;

    protected ?Closure $withBrowsershotCallback = null;

    protected ?string $diskName = null;

    protected string $diskVisibility = 'private';

    protected bool $inline = false;

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function html(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function width(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function height(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function size(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;

        return $this;
    }

    public function type(ImageType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function quality(int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function fullPage(bool $fullPage = true): self
    {
        $this->fullPage = $fullPage;

        return $this;
    }

    public function selector(string $selector): self
    {
        $this->selector = $selector;

        return $this;
    }

    public function clip(int $x, int $y, int $width, int $height): self
    {
        $this->clip = compact('x', 'y', 'width', 'height');

        return $this;
    }

    public function omitBackground(bool $omitBackground = true): self
    {
        $this->omitBackground = $omitBackground;

        return $this;
    }

    public function deviceScaleFactor(int $factor): self
    {
        $this->deviceScaleFactor = $factor;

        return $this;
    }

    public function waitForTimeout(int $milliseconds): self
    {
        $this->waitForTimeout = $milliseconds;

        return $this;
    }

    public function waitForSelector(string $selector): self
    {
        $this->waitForSelector = $selector;

        return $this;
    }

    public function waitUntil(string $event): self
    {
        $this->waitUntil = $event;

        return $this;
    }

    public function withBrowsershot(Closure $callback): self
    {
        $this->withBrowsershotCallback = $callback;

        return $this;
    }

    public function driver(string $name): self
    {
        $this->driverName = $name;

        return $this;
    }

    public function setDriver(ScreenshotDriver $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function inline(?string $name = null): self
    {
        $this->inline = true;

        if ($name) {
            $this->downloadName = $name;
        }

        return $this;
    }

    public function download(?string $name = null): self
    {
        $this->inline = false;

        if ($name) {
            $this->downloadName = $name;
        }

        return $this;
    }

    public function name(string $name): self
    {
        $this->downloadName = $name;

        return $this;
    }

    public function disk(string $diskName, string $visibility = 'private'): self
    {
        $this->diskName = $diskName;
        $this->diskVisibility = $visibility;

        return $this;
    }

    public function base64(): string
    {
        return base64_encode($this->generateScreenshot());
    }

    public function save(string $path): self
    {
        if ($this->diskName) {
            $this->saveOnDisk($path);

            return $this;
        }

        $this->getDriver()->saveScreenshot(
            $this->getInput(),
            $this->isHtml(),
            $this->buildOptions(),
            $path,
        );

        return $this;
    }

    public function saveQueued(
        string $path,
        ?string $connection = null,
        ?string $queue = null,
    ): QueuedScreenshotResponse {
        if ($this->withBrowsershotCallback !== null) {
            throw CouldNotTakeScreenshot::cannotQueueWithBrowsershotClosure();
        }

        $jobClass = config('laravel-screenshot.job');

        $job = new $jobClass(
            input: $this->getInput(),
            isHtml: $this->isHtml(),
            options: $this->buildOptions(),
            path: $path,
            diskName: $this->diskName,
            visibility: $this->diskVisibility,
            driverName: $this->driverName,
        );

        if ($connection) {
            $job->onConnection($connection);
        }

        if ($queue) {
            $job->onQueue($queue);
        }

        $dispatch = dispatch($job);

        return new QueuedScreenshotResponse($dispatch, $job);
    }

    public function toResponse($request): Response
    {
        $content = $this->generateScreenshot();

        $imageType = $this->buildOptions()->type ?? ImageType::Png;

        $headers = [
            'Content-Type' => $imageType->contentType(),
            'Content-Length' => strlen($content),
        ];

        $disposition = $this->inline ? 'inline' : 'attachment';
        $filename = $this->downloadName ?? "screenshot.{$imageType->value}";
        $headers['Content-Disposition'] = "{$disposition}; filename=\"{$filename}\"";

        return new Response($content, 200, $headers);
    }

    public function buildOptions(): ScreenshotOptions
    {
        $defaults = config('laravel-screenshot.defaults', []);

        return new ScreenshotOptions(
            width: $this->width ?? ($defaults['width'] ?? null),
            height: $this->height ?? ($defaults['height'] ?? null),
            type: $this->type ?? (isset($defaults['type']) ? ImageType::from($defaults['type']) : null),
            quality: $this->quality,
            fullPage: $this->fullPage,
            selector: $this->selector,
            clip: $this->clip,
            deviceScaleFactor: $this->deviceScaleFactor ?? ($defaults['device_scale_factor'] ?? null),
            omitBackground: $this->omitBackground,
            waitForTimeout: $this->waitForTimeout,
            waitForSelector: $this->waitForSelector,
            waitUntil: $this->waitUntil ?? ($defaults['wait_until'] ?? null),
        );
    }

    protected function generateScreenshot(): string
    {
        return $this->getDriver()->generateScreenshot(
            $this->getInput(),
            $this->isHtml(),
            $this->buildOptions(),
        );
    }

    protected function saveOnDisk(string $path): void
    {
        $fileName = pathinfo($path, PATHINFO_BASENAME);

        $temporaryDirectory = (new TemporaryDirectory)->create();

        $this->getDriver()->saveScreenshot(
            $this->getInput(),
            $this->isHtml(),
            $this->buildOptions(),
            $temporaryDirectory->path($fileName),
        );

        $content = file_get_contents($temporaryDirectory->path($fileName));

        $temporaryDirectory->delete();

        Storage::disk($this->diskName)->put($path, $content, $this->diskVisibility);
    }

    protected function getInput(): string
    {
        if ($this->html !== null) {
            return $this->html;
        }

        if ($this->url !== null) {
            return $this->url;
        }

        throw CouldNotTakeScreenshot::noInputProvided();
    }

    protected function isHtml(): bool
    {
        return $this->html !== null;
    }

    protected function getDriver(): ScreenshotDriver
    {
        if ($this->driver) {
            return $this->applyBrowsershotCallback($this->driver);
        }

        if ($this->driverName) {
            $driver = app("laravel-screenshot.driver.{$this->driverName}");

            if (! $driver instanceof ScreenshotDriver) {
                throw InvalidDriver::unknown($this->driverName);
            }

            return $this->applyBrowsershotCallback($driver);
        }

        return $this->applyBrowsershotCallback(app(ScreenshotDriver::class));
    }

    protected function applyBrowsershotCallback(ScreenshotDriver $driver): ScreenshotDriver
    {
        if ($this->withBrowsershotCallback) {
            if ($driver instanceof BrowsershotDriver) {
                $driver->customizeBrowsershot($this->withBrowsershotCallback);
            }
        }

        return $driver;
    }
}
