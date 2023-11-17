<?php

namespace FelixNagel\T3extblog\Event;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\AbstractSubscriber;

abstract class AbstractSubscriberEvent extends AbstractEvent
{
    public function __construct(protected AbstractSubscriber $subscriber)
    {
    }

    public function getSubscriber(): AbstractSubscriber
    {
        return $this->subscriber;
    }
}
