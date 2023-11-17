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
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class SubscribersEvent extends AbstractNotificationEvent
{
    public function __construct(
        protected Comment $comment,
        protected array|QueryResultInterface $subscribers,
        protected string $subject,
        protected array $variables
    ) {
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }

    public function getSubscribers(): array|QueryResultInterface
    {
        return $this->subscribers;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }
}
