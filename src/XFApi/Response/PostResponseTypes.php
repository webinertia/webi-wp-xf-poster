<?php
// todo: remove this class in next refactor
declare(strict_types=1);

namespace WebiXfBridge\XFApi\Response;

enum PostResponseTypes
{
    case Thread;
    case Post;
}
