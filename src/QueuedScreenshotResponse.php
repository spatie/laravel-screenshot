<?php

namespace Spatie\LaravelScreenshot;

use Closure;
use Illuminate\Foundation\Bus\PendingDispatch;
use Spatie\LaravelScreenshot\Jobs\GenerateScreenshotJob;

class QueuedScreenshotResponse
{
    public function __construct(
        protected PendingDispatch $dispatch,
        protected GenerateScreenshotJob $job,
    ) {}

    public function then(Closure $callback): static
    {
        $this->job->then($callback);

        return $this;
    }

    public function catch(Closure $callback): static
    {
        $this->job->catch($callback);

        return $this;
    }

    public function __call(string $method, array $parameters): static
    {
        $this->dispatch->{$method}(...$parameters);

        return $this;
    }
}
