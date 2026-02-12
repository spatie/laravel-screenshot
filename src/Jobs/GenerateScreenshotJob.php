<?php

namespace Spatie\LaravelScreenshot\Jobs;

use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Laravel\SerializableClosure\SerializableClosure;
use Spatie\LaravelScreenshot\Drivers\ScreenshotDriver;
use Spatie\LaravelScreenshot\ScreenshotOptions;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Throwable;

class GenerateScreenshotJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var SerializableClosure[] */
    protected array $thenCallbacks = [];

    /** @var SerializableClosure[] */
    protected array $catchCallbacks = [];

    public function __construct(
        public string $input,
        public bool $isHtml,
        public ScreenshotOptions $options,
        public string $path,
        public ?string $diskName = null,
        public string $visibility = 'private',
        public ?string $driverName = null,
    ) {}

    public function then(Closure $callback): self
    {
        $this->thenCallbacks[] = new SerializableClosure($callback);

        return $this;
    }

    public function catch(Closure $callback): self
    {
        $this->catchCallbacks[] = new SerializableClosure($callback);

        return $this;
    }

    public function handle(): void
    {
        $driver = $this->resolveDriver();

        $this->diskName
            ? $this->saveOnDisk($driver)
            : $driver->saveScreenshot($this->input, $this->isHtml, $this->options, $this->path);

        foreach ($this->thenCallbacks as $callback) {
            ($callback->getClosure())($this->path, $this->diskName);
        }
    }

    public function failed(Throwable $exception): void
    {
        foreach ($this->catchCallbacks as $callback) {
            ($callback->getClosure())($exception);
        }
    }

    protected function resolveDriver(): ScreenshotDriver
    {
        if ($this->driverName) {
            return app("laravel-screenshot.driver.{$this->driverName}");
        }

        return app(ScreenshotDriver::class);
    }

    protected function saveOnDisk(ScreenshotDriver $driver): void
    {
        $fileName = pathinfo($this->path, PATHINFO_BASENAME);

        $temporaryDirectory = (new TemporaryDirectory)->create();

        $driver->saveScreenshot(
            $this->input,
            $this->isHtml,
            $this->options,
            $temporaryDirectory->path($fileName),
        );

        $content = file_get_contents($temporaryDirectory->path($fileName));

        $temporaryDirectory->delete();

        Storage::disk($this->diskName)->put($this->path, $content, $this->visibility);
    }
}
