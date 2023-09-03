<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity as Message;
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
    public function createAction()
    {
        $this->checkAuth();
        $email = $this->authentication->getEmail();

        if (!$this->settings['blogSubscription']['subscribeForPosts']) {
            $this->addFlashMessageByKey('notAllowed', Message::ERROR);
            $this->redirect('list', 'PostSubscriber');
        }

        // check if user already registered
        $subscribers = $this->subscriberRepository->findExistingSubscriptions($email);
        if (count($subscribers) > 0) {
            $this->addFlashMessageByKey('alreadyRegistered', Message::NOTICE);
            $this->redirect('list', 'PostSubscriber');
        }

        /* @var $subscriber BlogSubscriber */
        $subscriber = $this->objectManager->get(BlogSubscriber::class);
        $subscriber->setEmail($email);
        $subscriber->setHidden(false);
        $subscriber->setSysLanguageUid(FrontendUtility::getLanguageUid());
        $subscriber->setPrivacyPolicyAccepted(true);

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
     * @param BlogSubscriber $subscriber
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
