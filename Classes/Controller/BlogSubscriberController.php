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
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity as Message;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use Psr\Http\Message\ResponseInterface;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;
use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use FelixNagel\T3extblog\Service\BlogNotificationService;
use FelixNagel\T3extblog\Utility\FrontendUtility;
use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * BlogSubscriberController.
 */
class BlogSubscriberController extends AbstractSubscriberController
{
    /**
     * @var BlogSubscriberRepository
     */
    protected $subscriberRepository;

    /**
     * @var PostSubscriber
     */
    protected $subscriber = null;

    public function __construct(BlogSubscriberRepository $subscriberRepository, protected BlogNotificationService $notificationService)
    {
        $this->subscriberRepository = $subscriberRepository;
    }

    protected function initializeAction()
    {
        parent::initializeAction();

        $this->subscriptionSettings = $this->settings['subscriptionManager']['blog']['subscriber'];
    }

    /**
     * Create a new subscription.
     */
    public function createAction(): ResponseInterface
    {
        if (($authResult = $this->checkAuth()) instanceof ResponseInterface) {
            return $authResult;
        }

        $email = $this->authentication->getEmail();

        if (!$this->settings['blogSubscription']['subscribeForPosts']) {
            $this->addFlashMessageByKey('notAllowed', Message::ERROR);
            return $this->redirect('list', 'PostSubscriber');
        }

        // check if user already registered
        $subscribers = $this->subscriberRepository->findExistingSubscriptions($email);
        if (count($subscribers) > 0) {
            $this->addFlashMessageByKey('alreadyRegistered', Message::NOTICE);
            return $this->redirect('list', 'PostSubscriber');
        }

        /* @var $subscriber BlogSubscriber */
        $subscriber = GeneralUtility::makeInstance(BlogSubscriber::class);
        $subscriber->setEmail($email);
        $subscriber->setHidden(false);
        $subscriber->setSysLanguageUid(FrontendUtility::getLanguageUid());
        $subscriber->setPrivacyPolicyAccepted(true);

        $this->subscriberRepository->add($subscriber);
        $this->persistAllEntities();
        $this->getLog()->dev('Added blog subscriber uid='.$subscriber->getUid());

        $this->notificationService->processNewEntity($subscriber);

        $this->addFlashMessageByKey('created');
        return $this->redirect('list', 'PostSubscriber');
    }

    protected function dispatchConfirmEvent(AbstractSubscriber $subscriber): AbstractSubscriber
    {
        /** @var Event\Post\SubscriberDeleteEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new Event\Post\SubscriberConfirmEvent($subscriber)
        );

        return $event->getSubscriber();
    }

    #[IgnoreValidation(['value' => 'subscriber'])]
    public function deleteAction(BlogSubscriber $subscriber = null): ResponseInterface
    {
        return $this->delete($subscriber);
    }

    protected function dispatchDeleteEvent(AbstractSubscriber $subscriber): AbstractSubscriber
    {
        /** @var Event\Post\SubscriberDeleteEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new Event\Post\SubscriberDeleteEvent($subscriber)
        );

        return $event->getSubscriber();
    }

    /**
     * Finds existing subscriptions.
     *
     * @param BlogSubscriber $subscriber
     */
    protected function findExistingSubscriptions($subscriber): QueryResultInterface
    {
        return $this->subscriberRepository->findExistingSubscriptions(
            $subscriber->getEmail(),
            $subscriber->getUid()
        );
    }
}
