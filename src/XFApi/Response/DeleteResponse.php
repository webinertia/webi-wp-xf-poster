<?php

declare(strict_types=1);

namespace WebiXfBridge\XFApi\Response;

use function json_decode;
use function wp_remote_retrieve_body;

final class DeleteResponse
{
    public $body;
    public $type;
    public bool $success = false;

    public function __construct(
        private array $response
    ) {
        $this->body = json_decode(wp_remote_retrieve_body($this->response));
        $this->success = $this->body->success;
    }
}
