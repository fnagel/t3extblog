<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use Psr\Http\Message\ResponseInterface;
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
     * @param PostSubscriber $subscriber
     */
    #[IgnoreValidation(['value' => 'subscriber'])]
    public function deleteAction($subscriber = null): ResponseInterface
    {
        parent::deleteAction($subscriber);

        return $this->htmlResponse();
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
