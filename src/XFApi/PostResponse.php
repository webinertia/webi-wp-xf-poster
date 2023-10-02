<?php

declare(strict_types=1);

namespace WebiXfBridge\XFApi;

use WebiXfBridge\Exception\InvalidLogicException;
use WebiXfBridge\XFApi\PostResponeTypes;
use WebiXfBridge\XFApiResponseInterface;

use function json_decode;
use function wp_remote_retrieve_body;

final class PostResponse
{
    public $body;
    public $type          = null;
    public ?int $postId   = null;
    public ?int $threadId = null;

    public function __construct(
        private array $response
    ) {
        $this->body = json_decode(wp_remote_retrieve_body($this->response));
        switch(true) {
            case isset($this->body->thread):
                $this->type = PostResponseTypes::Thread;
                $this->threadId = $this->body->thread->thread_id;
                $this->postId   = $this->body->thread->first_post_id;
                break;
            case isset($this->body->post):
                $this->type = PostResponseTypes::Post;
                if (! $this->body->post->is_first_post) {
                    throw new InvalidLogicException('Invalid post editing detected.');
                }
                $this->threadId = $this->body->post->thread_id;
                $this->postId   = $this->body->post->post_id;
                break;
            default:
                throw new InvalidLogicException('Unsupported ResponseType Detected');
                break;
        }
        return $this;
    }
}