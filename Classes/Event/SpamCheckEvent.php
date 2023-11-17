<?php

namespace FelixNagel\T3extblog\Event;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class SpamCheckEvent extends AbstractEvent
{
    public function __construct(
        protected readonly array $settings,
        protected readonly array $arguments,
        protected int $spamPoints
    ) {
    }

    public function getSpamPoints(): int
    {
        return $this->spamPoints;
    }
}
