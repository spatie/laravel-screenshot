<?php

namespace Spatie\LaravelScreenshot\Drivers;

use Closure;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelScreenshot\Exceptions\CouldNotTakeScreenshot;
use Spatie\LaravelScreenshot\ScreenshotOptions;

class BrowsershotDriver implements ScreenshotDriver
{
    protected array $config;

    protected ?Closure $customizeBrowsershot = null;

    public function __construct(array $config = [])
    {
        if (! class_exists(Browsershot::class)) {
            throw CouldNotTakeScreenshot::browsershotNotInstalled();
        }

        $this->config = $config;
    }

    public function customizeBrowsershot(?Closure $callback): self
    {
        $this->customizeBrowsershot = $callback;

        return $this;
    }

    public function generateScreenshot(string $input, bool $isHtml, ScreenshotOptions $options): string
    {
        $browsershot = $this->buildBrowsershot($input, $isHtml, $options);

        return $browsershot->screenshot();
    }

    public function saveScreenshot(string $input, bool $isHtml, ScreenshotOptions $options, string $path): void
    {
        $browsershot = $this->buildBrowsershot($input, $isHtml, $options);

        $browsershot->save($path);
    }

    protected function buildBrowsershot(string $input, bool $isHtml, ScreenshotOptions $options): Browsershot
    {
        $browsershot = $isHtml
            ? Browsershot::html($input)
            : Browsershot::url($input);

        if ($options->width !== null && $options->height !== null) {
            $browsershot->windowSize($options->width, $options->height);
        }

        if ($options->deviceScaleFactor !== null) {
            $browsershot->deviceScaleFactor($options->deviceScaleFactor);
        }

        if ($options->type !== null) {
            $browsershot->setScreenshotType($options->type->value, $options->quality);
        }

        if ($options->fullPage === true) {
            $browsershot->fullPage();
        }

        if ($options->selector !== null) {
            $browsershot->select($options->selector);
        }

        if ($options->clip !== null) {
            $browsershot->clip(
                $options->clip['x'],
                $options->clip['y'],
                $options->clip['width'],
                $options->clip['height'],
            );
        }

        if ($options->omitBackground === true) {
            $browsershot->hideBackground();
            $browsershot->transparentBackground();
        }

        if ($options->waitUntil !== null) {
            $browsershot->waitUntilNetworkIdle($options->waitUntil === 'networkidle0');
        }

        if ($options->waitForSelector !== null) {
            $browsershot->waitForFunction("document.querySelector('{$options->waitForSelector}') !== null");
        }

        if ($options->waitForTimeout !== null) {
            $browsershot->setDelay($options->waitForTimeout);
        }

        $this->applyConfigurationDefaults($browsershot);

        if ($this->customizeBrowsershot) {
            ($this->customizeBrowsershot)($browsershot);
        }

        return $browsershot;
    }

    protected function applyConfigurationDefaults(Browsershot $browsershot): void
    {
        if ($nodeBinary = ($this->config['node_binary'] ?? null)) {
            $browsershot->setNodeBinary($nodeBinary);
        }

        if ($npmBinary = ($this->config['npm_binary'] ?? null)) {
            $browsershot->setNpmBinary($npmBinary);
        }

        if ($includePath = ($this->config['include_path'] ?? null)) {
            $browsershot->setIncludePath($includePath);
        }

        if ($chromePath = ($this->config['chrome_path'] ?? null)) {
            $browsershot->setChromePath($chromePath);
        }

        if ($nodeModulesPath = ($this->config['node_modules_path'] ?? null)) {
            $browsershot->setNodeModulePath($nodeModulesPath);
        }

        if ($binPath = ($this->config['bin_path'] ?? null)) {
            $browsershot->setBinPath($binPath);
        }

        if ($tempPath = ($this->config['temp_path'] ?? null)) {
            $browsershot->setCustomTempPath($tempPath);
        }

        if ($this->config['write_options_to_file'] ?? false) {
            $browsershot->writeOptionsToFile();
        }

        if ($this->config['no_sandbox'] ?? false) {
            $browsershot->noSandbox();
        }
    }
}
