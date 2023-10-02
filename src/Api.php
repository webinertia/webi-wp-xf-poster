<?php

declare(strict_types=1);

namespace WebiXfBridge;

enum Api: string
{
    /**
     * POST method will update the post with passed id
     * GET method will return information about the post
     * DELETE method deletes the post matching id
     */
    case threads = '/api/threads/';
    case posts   = '/api/posts/';
}
