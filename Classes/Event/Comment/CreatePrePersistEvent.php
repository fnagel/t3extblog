<?php

namespace FelixNagel\T3extblog\Event\Comment;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Event\AbstractEvent;

class CreatePrePersistEvent extends AbstractEvent
{
    public function __construct(protected Post $post, protected Comment $comment)
    {
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}
