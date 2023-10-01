<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\BridgeInterface;
use WebiXfBridge\Settings;
use WebiXfBridge\XfApi;

use function get_option;

final class Request
{
    public const POST_HEADER_TYPE = 'Content-type: application/x-www-form-urlencoded';
    private ?string $apiPath;
    private ?string $apiUrl;

    public function __construct()
    {

    }

    public function setAPiPath(string $apiPath): void
    {

    }

    public function getApiPath(): string|null
    {
        return $this->apiPath;
    }

    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

    public function getApiUrl(): string|null
    {
        return $this->apiUrl;
    }
}