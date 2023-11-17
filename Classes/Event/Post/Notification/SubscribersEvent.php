<?php

namespace FelixNagel\T3extblog\Event\Post\Notification;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Event\AbstractNotificationEvent;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class SubscribersEvent extends AbstractNotificationEvent
{
    public function __construct(
        protected readonly Post $post,
        protected array|QueryResultInterface $subscribers,
        protected string $subject,
        protected array $variables
    ) {
    }

    public function getPost(): Post
    {
        return $this->post;
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
