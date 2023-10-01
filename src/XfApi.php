<?php

declare(strict_types=1);

namespace WebiXfBridge;

enum XfApi: string
{
    /**
     * POST method will update the post with passed id
     * GET method will return information about the post
     * DELETE method deletes the post matching id
     */
    case Threads = '/api/threads/';
    case Posts   = '/api/posts/';
}
