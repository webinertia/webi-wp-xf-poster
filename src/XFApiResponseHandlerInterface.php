<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\XFApi\PostResponse;

interface XFApiResponseHandlerInterface
{
    public function handlePostResponse(PostResponse $response, int $wpPostId);
    public function saveMetaData(int $wpPostId, string $column, int $contextId): int|bool;
}
