<?php

namespace FelixNagel\T3extblog\Event\Post\Notification;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;
use FelixNagel\T3extblog\Event\AbstractNotificationEvent;

class CreateEvent extends AbstractNotificationEvent
{
    public function __construct(protected BlogSubscriber $subscriber)
    {
    }

    public function getSubscriber(): BlogSubscriber
    {
        return $this->subscriber;
    }
}
