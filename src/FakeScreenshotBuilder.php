<?php

declare(strict_types=1);

namespace Spatie\LaravelScreenshot;

use Closure;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

class FakeScreenshotBuilder extends ScreenshotBuilder
{
    protected bool $respondedWithScreenshot = false;

    /** @var array<int, array{builder: ScreenshotBuilder, path: string}> */
    protected array $savedScreenshots = [];

    /** @var array<int, array{builder: ScreenshotBuilder, path: string}> */
    protected array $queuedScreenshots = [];

    public function save(string $path): self
    {
        $this->savedScreenshots[] = ['builder' => clone $this, 'path' => $path];

        return $this;
    }

    public function toResponse($request): Response
    {
        $this->respondedWithScreenshot = true;

        $imageType = $this->buildOptions()->type ?? Enums\ImageType::Png;

        $headers = [
            'Content-Type' => $imageType->contentType(),
        ];

        $disposition = $this->inline ? 'inline' : 'attachment';
        $filename = $this->downloadName ?? 'screenshot.'.$imageType->value;
        $headers['Content-Disposition'] = "{$disposition}; filename=\"{$filename}\"";

        return new Response('', 200, $headers);
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
            $found = false;
            foreach ($this->savedScreenshots as $saved) {
                if ($pathOrCallback($saved['builder'], $saved['path'])) {
                    $found = true;
                    break;
                }
            }
            Assert::assertTrue($found, 'No saved screenshot matched the given callback.');

            return;
        }

        $paths = array_column($this->savedScreenshots, 'path');
        Assert::assertContains($pathOrCallback, $paths, "No screenshot was saved to [{$pathOrCallback}].");
    }

    public function assertUrlIs(string $expectedUrl): void
    {
        $allBuilders = array_merge(
            array_column($this->savedScreenshots, 'builder'),
            array_column($this->queuedScreenshots, 'builder'),
        );

        if ($this->respondedWithScreenshot) {
            $allBuilders[] = $this;
        }

        Assert::assertNotEmpty($allBuilders, 'No screenshots were taken.');

        $found = false;
        foreach ($allBuilders as $builder) {
            if ($builder->url === $expectedUrl) {
                $found = true;
                break;
            }
        }

        Assert::assertTrue($found, "No screenshot was taken of URL [{$expectedUrl}].");
    }

    public function assertHtmlContains(string $expectedHtml): void
    {
        $allBuilders = array_merge(
            array_column($this->savedScreenshots, 'builder'),
            array_column($this->queuedScreenshots, 'builder'),
        );

        if ($this->respondedWithScreenshot) {
            $allBuilders[] = $this;
        }

        Assert::assertNotEmpty($allBuilders, 'No screenshots were taken.');

        $found = false;
        foreach ($allBuilders as $builder) {
            if ($builder->html !== null && str_contains($builder->html, $expectedHtml)) {
                $found = true;
                break;
            }
        }

        Assert::assertTrue($found, "No screenshot HTML contained [{$expectedHtml}].");
    }

    public function assertRespondedWithScreenshot(?Closure $callback = null): void
    {
        Assert::assertTrue($this->respondedWithScreenshot, 'No screenshot was returned as a response.');

        if ($callback) {
            Assert::assertTrue($callback($this), 'The response screenshot did not match the given callback.');
        }
    }

    public function assertQueued(string|Closure|null $pathOrCallback = null): void
    {
        Assert::assertNotEmpty($this->queuedScreenshots, 'No screenshots were queued.');

        if ($pathOrCallback === null) {
            return;
        }

        if ($pathOrCallback instanceof Closure) {
            $found = false;
            foreach ($this->queuedScreenshots as $queued) {
                if ($pathOrCallback($queued['builder'], $queued['path'])) {
                    $found = true;
                    break;
                }
            }
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
                    'A queued screenshot matched the given callback.'
                );
            }

            return;
        }

        $paths = array_column($this->queuedScreenshots, 'path');
        Assert::assertNotContains($pathOrCallback, $paths, "A screenshot was unexpectedly queued to [{$pathOrCallback}].");
    }
}
