<?php

declare(strict_types=1);

namespace WebiXfBridge\XFApi\Response;

use WebiXfBridge\XFApi\Response\PostResponse;

interface ResponseHandlerInterface
{
    public function handlePostResponse(PostResponse $response, int $wpPostId);
    public function saveMetaData(int $wpPostId, string $column, int $contextId): int|bool;
}
