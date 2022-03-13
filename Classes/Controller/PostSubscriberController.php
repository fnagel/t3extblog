<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * SubscriberController.
 */
class PostSubscriberController extends AbstractSubscriberController
{
    /**
     * @var PostSubscriberRepository
     */
    protected $subscriberRepository;

    /**
     * @var PostSubscriber
     */
    protected $subscriber = null;

    public function __construct(PostSubscriberRepository $subscriberRepository)
    {
        $this->subscriberRepository = $subscriberRepository;
    }

    protected function initializeAction()
    {
        parent::initializeAction();

        $this->subscriptionSettings = $this->settings['subscriptionManager']['comment']['subscriber'];
    }

    /**
     * Do not remove @param (needed for Extbase)
     *
     * @param \FelixNagel\T3extblog\Domain\Model\PostSubscriber $subscriber
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("subscriber")
     */
    public function deleteAction($subscriber = null)
    {
        parent::deleteAction($subscriber);
    }

    /**
     * Finds existing subscriptions.
     *
     * @param PostSubscriber $subscriber
     */
    protected function findExistingSubscriptions($subscriber): QueryResultInterface
    {
        return $this->subscriberRepository->findExistingSubscriptions(
            $subscriber->getPostUid(),
            $subscriber->getEmail(),
            $subscriber->getUid()
        );
    }
}
