<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use FelixNagel\T3extblog\Service\BlogNotificationService;
use FelixNagel\T3extblog\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;

/**
 * BlogSubscriberController.
 */
class BlogSubscriberController extends AbstractSubscriberController
{
    /**
     * subscriberRepository.
     *
     * @var BlogSubscriberRepository
     */
    protected $subscriberRepository;

    /**
     * Notification Service.
     *
     * @var BlogNotificationService
     */
    protected $notificationService;

    /**
     * subscriber.
     *
     * @var \FelixNagel\T3extblog\Domain\Model\PostSubscriber
     */
    protected $subscriber = null;

    /**
     * BlogSubscriberController constructor.
     *
     * @param BlogSubscriberRepository $subscriberRepository
     * @param BlogNotificationService $notificationService
     */
    public function __construct(BlogSubscriberRepository $subscriberRepository, BlogNotificationService $notificationService)
    {
        $this->subscriberRepository = $subscriberRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeAction()
    {
        parent::initializeAction();

        $this->subscriptionSettings = $this->settings['subscriptionManager']['blog']['subscriber'];
    }

    /**
     * action list.
     */
    public function listAction()
    {
        $this->checkAuth();

        $this->redirect('list', 'Subscriber');
    }

    /**
     * Displays a form (create) or a button (delete).
     */
    public function createAction()
    {
        $this->checkAuth();
        $email = $this->authentication->getEmail();

        if (!$this->settings['blogSubscription']['subscribeForPosts']) {
            $this->addFlashMessageByKey('notAllowed', FlashMessage::ERROR);
            $this->redirect('list', 'PostSubscriber');
        }

        // check if user already registered
        $subscribers = $this->subscriberRepository->findExistingSubscriptions($email);
        if (count($subscribers) > 0) {
            $this->addFlashMessageByKey('alreadyRegistered', FlashMessage::NOTICE);
            $this->redirect('list', 'PostSubscriber');
        }

        /* @var $subscriber BlogSubscriber */
        $subscriber = $this->objectManager->get(BlogSubscriber::class);
        $subscriber->setEmail($email);
        $subscriber->setHidden(false);
        $subscriber->setSysLanguageUid((int) GeneralUtility::getLanguageUid());
        $subscriber->setPrivacyPolicyAccepted(true);

        $this->subscriberRepository->add($subscriber);
        $this->persistAllEntities();
        $this->getLog()->dev('Added blog subscriber uid='.$subscriber->getUid());

        $this->notificationService->processNewEntity($subscriber);

        $this->addFlashMessageByKey('created');
        $this->redirect('list', 'PostSubscriber');
    }

    /**
     * action delete.
     *
     * @param \FelixNagel\T3extblog\Domain\Model\BlogSubscriber $subscriber
     */
    public function deleteAction($subscriber = null)
    {
        parent::deleteAction($subscriber);
    }

    /**
     * Finds existing subscriptions.
     *
     * @param BlogSubscriber $subscriber
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    protected function findExistingSubscriptions($subscriber)
    {
        return $this->subscriberRepository->findExistingSubscriptions(
            $subscriber->getEmail(),
            $subscriber->getUid()
        );
    }
}
