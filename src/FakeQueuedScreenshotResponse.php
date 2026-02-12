<?php

declare(strict_types=1);

namespace Spatie\LaravelScreenshot;

use Closure;

class FakeQueuedScreenshotResponse extends QueuedScreenshotResponse
{
    public function __construct()
    {
        // No-op: skip parent constructor since we don't need a real PendingDispatch
    }

    public function then(Closure $callback): static
    {
        return $this;
    }

    public function catch(Closure $callback): static
    {
        return $this;
    }

    public function __call(string $method, array $parameters): static
    {
        return $this;
    }
}
