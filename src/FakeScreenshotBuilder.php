<?php

namespace Spatie\LaravelScreenshot;

use Closure;
use PHPUnit\Framework\Assert;

class FakeScreenshotBuilder extends ScreenshotBuilder
{
    /** @var array<int, array{builder: ScreenshotBuilder, path: string}> */
    protected array $savedScreenshots = [];

    /** @var array<int, array{builder: ScreenshotBuilder, path: string}> */
    protected array $queuedScreenshots = [];

    public function save(string $path): self
    {
        $this->savedScreenshots[] = ['builder' => clone $this, 'path' => $path];

        return $this;
    }

    public function saveQueued(
        string $path,
        ?string $connection = null,
        ?string $queue = null,
    ): QueuedScreenshotResponse {
        $this->queuedScreenshots[] = ['builder' => clone $this, 'path' => $path];

        return new FakeQueuedScreenshotResponse;
    }

    public function assertSaved(string|Closure|null $pathOrCallback = null): void
    {
        Assert::assertNotEmpty($this->savedScreenshots, 'No screenshots were saved.');

        if ($pathOrCallback === null) {
            return;
        }

        if ($pathOrCallback instanceof Closure) {
            $found = collect($this->savedScreenshots)->contains(
                fn (array $saved) => $pathOrCallback($saved['builder'], $saved['path']),
            );

            Assert::assertTrue($found, 'No saved screenshot matched the given callback.');

            return;
        }

        $paths = array_column($this->savedScreenshots, 'path');

        Assert::assertContains($pathOrCallback, $paths, "No screenshot was saved to [{$pathOrCallback}].");
    }

    public function assertUrlIs(string $expectedUrl): void
    {
        $allBuilders = $this->getAllBuilders();

        Assert::assertNotEmpty($allBuilders, 'No screenshots were taken.');

        $found = collect($allBuilders)->contains(
            fn (ScreenshotBuilder $builder) => $builder->url === $expectedUrl,
        );

        Assert::assertTrue($found, "No screenshot was taken of URL [{$expectedUrl}].");
    }

    public function assertHtmlContains(string $expectedHtml): void
    {
        $allBuilders = $this->getAllBuilders();

        Assert::assertNotEmpty($allBuilders, 'No screenshots were taken.');

        $found = collect($allBuilders)->contains(
            fn (ScreenshotBuilder $builder) => $builder->html !== null
                && str_contains($builder->html, $expectedHtml),
        );

        Assert::assertTrue($found, "No screenshot HTML contained [{$expectedHtml}].");
    }

    public function assertQueued(string|Closure|null $pathOrCallback = null): void
    {
        Assert::assertNotEmpty($this->queuedScreenshots, 'No screenshots were queued.');

        if ($pathOrCallback === null) {
            return;
        }

        if ($pathOrCallback instanceof Closure) {
            $found = collect($this->queuedScreenshots)->contains(
                fn (array $queued) => $pathOrCallback($queued['builder'], $queued['path']),
            );

            Assert::assertTrue($found, 'No queued screenshot matched the given callback.');

            return;
        }

        $paths = array_column($this->queuedScreenshots, 'path');

        Assert::assertContains($pathOrCallback, $paths, "No screenshot was queued to [{$pathOrCallback}].");
    }

    public function assertNotQueued(string|Closure|null $pathOrCallback = null): void
    {
        if ($pathOrCallback === null) {
            Assert::assertEmpty($this->queuedScreenshots, 'Screenshots were unexpectedly queued.');

            return;
        }

        if ($pathOrCallback instanceof Closure) {
            foreach ($this->queuedScreenshots as $queued) {
                Assert::assertFalse(
                    $pathOrCallback($queued['builder'], $queued['path']),
                    'A queued screenshot matched the given callback.',
                );
            }

            return;
        }

        $paths = array_column($this->queuedScreenshots, 'path');

        Assert::assertNotContains($pathOrCallback, $paths, "A screenshot was unexpectedly queued to [{$pathOrCallback}].");
    }

    /** @return array<int, ScreenshotBuilder> */
    protected function getAllBuilders(): array
    {
        $allBuilders = array_merge(
            array_column($this->savedScreenshots, 'builder'),
            array_column($this->queuedScreenshots, 'builder'),
        );

        return $allBuilders;
    }
}
