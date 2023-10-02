<?php

declare(strict_types=1);

namespace WebiXfBridge;

use WebiXfBridge\BridgeInterface;
use WebiXfBridge\XFApiResponseHandlerInterface;
use WebiXfBridge\XFApi\PostResponse;

final class ResponseHandler implements XFApiResponseHandlerInterface
{
    public function handlePostResponse(PostResponse $response, $wpPostId)
    {
        $this->saveMetaData($wpPostId, BridgeInterface::THREAD_ID_COLUMN, $response->threadId);
        $this->saveMetaData($wpPostId, BridgeInterface::POST_ID_COLUMN, $response->postId);
    }

    public function saveMetaData(int $wpPostId, string $column, int $contextId): int|bool
    {
        return update_post_meta($wpPostId, $column, $contextId);
    }
}
