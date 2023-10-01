<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\XfApi;

final class Request
{
    public const POST_HEADER_TYPE = 'Content-type: application/x-www-form-urlencoded';
    private ?string $endPoint;
    private ?string $domain;
    private ?string $scheme = 'https';

    public function __construct()
    {

    }

    public function setEndPoint(string $endPoint): void
    {

    }
}