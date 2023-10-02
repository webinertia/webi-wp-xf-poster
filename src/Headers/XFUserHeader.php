<?php

declare(strict_types=1);

namespace WebiXfBridge\Headers;

use WebiXfBridge\Settings;

use function get_option;

final class XFUserHeader
{
    private array $header;

    public function __construct()
    {
        $this->header = [
            'XF-Api-User' => get_option(Settings::xfUserIdSetting->value),
        ];
        return $this;
    }

    public function getHeader(): array
    {
        return $this->header;
    }
}
