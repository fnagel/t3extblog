<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\AbstractSubscriberRepository;

abstract class AbstractSubscriberWidget extends AbstractListWidget
{
    /**
     * @inheritDoc
     */
    protected $templateName = 'SubscriberWidget';

    /**
     * @var AbstractSubscriberRepository
     */
    protected $subscriberRepository;

    /**
     * @return array
     */
    protected function getListItems()
    {
        return $this->subscriberRepository->findByPage($this->getStoragePids(), false);
    }
}
