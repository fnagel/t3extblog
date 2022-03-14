<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\PostSubscriber;
use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use FelixNagel\T3extblog\Service\BlogNotificationService;
use FelixNagel\T3extblog\Utility\FrontendUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
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

    
    protected BlogNotificationService $notificationService;

    /**
     * @var PostSubscriber
     */
    protected $subscriber = null;

    public function __construct(BlogSubscriberRepository $subscriberRepository, BlogNotificationService $notificationService)
    {
        $this->subscriberRepository = $subscriberRepository;
        $this->notificationService = $notificationService;
    }

    protected function initializeAction()
    {
        parent::initializeAction();

        $this->subscriptionSettings = $this->settings['subscriptionManager']['blog']['subscriber'];
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
        $subscriber->setSysLanguageUid((int) FrontendUtility::getLanguageUid());

        $this->subscriberRepository->add($subscriber);
        $this->persistAllEntities();
        $this->getLog()->dev('Added blog subscriber uid='.$subscriber->getUid());

        $this->notificationService->processNewEntity($subscriber);

        $this->addFlashMessageByKey('created');
        $this->redirect('list', 'PostSubscriber');
    }

    /**
     * Do not remove @param (needed for Extbase)
     *
     * @param \FelixNagel\T3extblog\Domain\Model\BlogSubscriber $subscriber
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("subscriber")
     */
    public function deleteAction($subscriber = null)
    {
        parent::deleteAction($subscriber);
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
