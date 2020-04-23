<?php

namespace FelixNagel\T3extblog\Dashboard\Provider;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\AbstractSubscriberRepository;

class SubscriberListDataProvider extends AbstractListDataProvider
{
    /**
     * @var AbstractSubscriberRepository
     */
    protected $subscriberRepository;

    /**
     * @param AbstractSubscriberRepository $subscriberRepository
     */
    public function __construct(AbstractSubscriberRepository $subscriberRepository)
    {
        $this->subscriberRepository = $subscriberRepository;
    }

    public function getItems(): array
    {
        return $this->subscriberRepository->findByPage($this->getStoragePids(), false)->toArray();
    }
}
