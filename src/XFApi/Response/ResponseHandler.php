<?php

declare(strict_types=1);

namespace WebiXfBridge\XFApi\Response;

use WebiXfBridge\BridgeInterface;
use WebiXfBridge\XFApi\Response\PostResponse;

use function delete_post_meta;
use function update_post_meta;

final class ResponseHandler implements ResponseHandlerInterface
{
    public function handlePostResponse(PostResponse $response, $wpPostId): void
    {
        $this->saveMetaData($wpPostId, BridgeInterface::THREAD_ID_COLUMN, $response->threadId);
        $this->saveMetaData($wpPostId, BridgeInterface::POST_ID_COLUMN, $response->postId);
    }

    public function handleDeleteResponse(DeleteResponse $response, $wpPostId): void
    {
        // remove related post metadata
        if ($response->success) {
            $this->deleteMetaData($wpPostId, BridgeInterface::THREAD_ID_COLUMN);
            $this->deleteMetaData($wpPostId, BridgeInterface::POST_ID_COLUMN);
        }
    }

    public function deleteMetaData(int $wpPostId, string $column): int|bool
    {
        return delete_post_meta($wpPostId, $column);
    }

    public function saveMetaData(int $wpPostId, string $column, int $contextId): int|bool
    {
        return update_post_meta($wpPostId, $column, $contextId);
    }
}
