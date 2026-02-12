<?php


namespace Spatie\LaravelScreenshot\Drivers;

use Illuminate\Support\Facades\Http;
use Spatie\LaravelScreenshot\Exceptions\CouldNotTakeScreenshot;
use Spatie\LaravelScreenshot\ScreenshotOptions;

class CloudflareDriver implements ScreenshotDriver
{
    protected string $apiToken;

    protected string $accountId;

    public function __construct(array $config = [])
    {
        $this->apiToken = $config['api_token'] ?? '';
        $this->accountId = $config['account_id'] ?? '';

        if (empty($this->apiToken)) {
            throw CouldNotTakeScreenshot::missingCloudflareCredentials();
        }

        if (empty($this->accountId)) {
            throw CouldNotTakeScreenshot::missingCloudflareCredentials();
        }
    }

    public function generateScreenshot(string $input, bool $isHtml, ScreenshotOptions $options): string
    {
        $requestBody = $this->buildRequestBody($input, $isHtml, $options);

        $response = Http::withToken($this->apiToken)
            ->post($this->endpoint(), $requestBody);

        if (! $response->successful()) {
            throw CouldNotTakeScreenshot::cloudflareApiError($response->body());
        }

        return $response->body();
    }

    public function saveScreenshot(string $input, bool $isHtml, ScreenshotOptions $options, string $path): void
    {
        $content = $this->generateScreenshot($input, $isHtml, $options);

        file_put_contents($path, $content);
    }

    protected function endpoint(): string
    {
        return "https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/browser-rendering/screenshot";
    }

    protected function buildRequestBody(string $input, bool $isHtml, ScreenshotOptions $options): array
    {
        $body = [];

        if ($isHtml) {
            $body['html'] = $input;
        } else {
            $body['url'] = $input;
        }

        $screenshotOptions = [];

        if ($options->type !== null) {
            $screenshotOptions['type'] = $options->type->value;
        }

        if ($options->quality !== null) {
            $screenshotOptions['quality'] = $options->quality;
        }

        if ($options->fullPage === true) {
            $screenshotOptions['fullPage'] = true;
        }

        if ($options->omitBackground === true) {
            $screenshotOptions['omitBackground'] = true;
        }

        if ($options->clip !== null) {
            $screenshotOptions['clip'] = $options->clip;
        }

        if ($options->selector !== null) {
            $screenshotOptions['selector'] = $options->selector;
        }

        if (! empty($screenshotOptions)) {
            $body['screenshotOptions'] = $screenshotOptions;
        }

        $viewport = [];

        if ($options->width !== null) {
            $viewport['width'] = $options->width;
        }

        if ($options->height !== null) {
            $viewport['height'] = $options->height;
        }

        if ($options->deviceScaleFactor !== null) {
            $viewport['deviceScaleFactor'] = $options->deviceScaleFactor;
        }

        if (! empty($viewport)) {
            $body['viewport'] = $viewport;
        }

        if ($options->waitUntil !== null) {
            $body['gotoOptions'] = [
                'waitUntil' => $options->waitUntil,
            ];
        }

        if ($options->waitForSelector !== null) {
            $body['waitForSelector'] = [
                'selector' => $options->waitForSelector,
            ];
        }

        if ($options->waitForTimeout !== null) {
            $body['waitForTimeout'] = $options->waitForTimeout;
        }

        return $body;
    }
}
