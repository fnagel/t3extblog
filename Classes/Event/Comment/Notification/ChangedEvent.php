<?php

namespace FelixNagel\T3extblog\Event\Comment\Notification;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Event\AbstractNotificationEvent;

class ChangedEvent extends AbstractNotificationEvent
{
    public function __construct(protected Comment $comment)
    {
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}
