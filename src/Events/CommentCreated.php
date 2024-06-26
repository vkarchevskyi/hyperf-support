<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace OnixSystemsPHP\HyperfSupport\Events;

use OnixSystemsPHP\HyperfSupport\Contract\CommentEvent;
use OnixSystemsPHP\HyperfSupport\Model\Comment;

class CommentCreated implements CommentEvent
{
    public function __construct(public Comment $comment) {}
}
