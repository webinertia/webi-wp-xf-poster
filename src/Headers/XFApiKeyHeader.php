<?php

declare(strict_types=1);

namespace WebiXfBridge\Headers;

use WebiXfBridge\Settings;

use function get_option;

final class XFApiKeyHeader
{
    private readonly array $header;

    public function __construct()
    {
        $this->header = [
            'XF-Api-Key' => get_option(Settings::apiKeySetting->value)
        ];
        return $this;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    // public function __invoke(): self
    // {
    //     return new self();
    // }
}
