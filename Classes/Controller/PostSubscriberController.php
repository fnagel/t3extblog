<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\AbstractSubscriber;
use FelixNagel\T3extblog\Event;
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

    protected function initializeAction(): void
    {
        parent::initializeAction();

        $this->subscriptionSettings = $this->settings['subscriptionManager']['comment']['subscriber'];
    }

    protected function dispatchConfirmEvent(AbstractSubscriber $subscriber): AbstractSubscriber
    {
        /** @var Event\Comment\SubscriberDeleteEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new Event\Comment\SubscriberConfirmEvent($subscriber)
        );

        return $event->getSubscriber();
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

    protected function dispatchDeleteEvent(AbstractSubscriber $subscriber): AbstractSubscriber
    {
        /** @var Event\Comment\SubscriberDeleteEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new Event\Comment\SubscriberDeleteEvent($subscriber)
        );

        return $event->getSubscriber();
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
