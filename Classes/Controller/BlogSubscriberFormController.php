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
use FelixNagel\T3extblog\Service\SpamCheckServiceInterface;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;

/**
 * BlogSubscriberFormController.
 */
class BlogSubscriberFormController extends AbstractController
{
    /**
     * blogSubscriberRepository.
     *
     * @var BlogSubscriberRepository
     */
    protected $blogSubscriberRepository;

    /**
     * Notification Service.
     *
     * @var BlogNotificationService
     */
    protected $notificationService;

    /**
     * Spam Check Service.
     *
     * @var SpamCheckServiceInterface
     */
    protected $spamCheckService;

    /**
     * BlogSubscriberFormController constructor.
     *
     * @param BlogSubscriberRepository $blogSubscriberRepository
     * @param BlogNotificationService $notificationService
     * @param SpamCheckServiceInterface $spamCheckService
     */
    public function __construct(
        BlogSubscriberRepository $blogSubscriberRepository,
        BlogNotificationService $notificationService,
        SpamCheckServiceInterface $spamCheckService
    ) {
        $this->blogSubscriberRepository = $blogSubscriberRepository;
        $this->notificationService = $notificationService;
        $this->spamCheckService = $spamCheckService;
    }

    /**
     * action new.
     *
     * @param BlogSubscriber $subscriber
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("subscriber")
     */
    public function newAction(BlogSubscriber $subscriber = null)
    {
        /* @var $subscriber BlogSubscriber */
        if ($subscriber === null) {
            $subscriber = $this->objectManager->get(BlogSubscriber::class);
        }

        $this->view->assign('subscriber', $subscriber);
    }

    /**
     * Adds a subscriber.
     *
     * @param BlogSubscriber $subscriber
     */
    public function createAction(BlogSubscriber $subscriber = null)
    {
        if ($subscriber === null) {
            $this->redirect('new');
        }

        if (!$this->settings['blogSubscription']['subscribeForPosts']) {
            $this->addFlashMessageByKey('notAllowed', FlashMessage::ERROR);
            $this->errorAction();
        }

        $this->checkSpamPoints();

        // check if user already registered
        $subscribers = $this->blogSubscriberRepository->findExistingSubscriptions($subscriber->getEmail());
        if (count($subscribers) > 0) {
            $this->addFlashMessageByKey('alreadyRegistered', FlashMessage::INFO);
            $this->errorAction();
        }

        $subscriber->setSysLanguageUid((int) $GLOBALS['TSFE']->sys_language_uid);

        $this->blogSubscriberRepository->add($subscriber);
        $this->persistAllEntities();
        $this->getLog()->dev('Added blog subscriber uid='.$subscriber->getUid());

        $this->notificationService->processNewEntity($subscriber);

        $this->addFlashMessageByKey('created');
        $this->redirect('success');
    }

    /**
     * Process SPAM point.
     */
    protected function checkSpamPoints()
    {
        $settings = $this->settings['blogSubscription']['spamCheck'];
        $threshold = $settings['threshold'];

        $spamPoints = $this->spamCheckService->process($settings);
        $logData = ['spamPoints' => $spamPoints];

        // block comment and redirect user
        if ($threshold['redirect'] > 0 && $spamPoints >= intval($threshold['redirect'])) {
            $this->getLog()->notice('New blog subscriber blocked and user redirected because of SPAM.', $logData);
            $this->redirect('', null, null, $settings['redirect']['arguments'], intval($settings['redirect']['pid']), $statusCode = 403);
        }

        // block comment and show message
        if ($threshold['block'] > 0 && $spamPoints >= intval($threshold['block'])) {
            $this->getLog()->notice('New blog subscriber blocked because of SPAM.', $logData);
            $this->addFlashMessageByKey('blockedAsSpam', FlashMessage::ERROR);
            $this->errorAction();
        }
    }

    /**
     * action success.
     */
    public function successAction()
    {
        if (!$this->hasFlashMessages()) {
            $this->redirect('new');
        }
    }

    /**
     * Disable error flash message.
     *
     * @return string|bool
     */
    protected function getErrorFlashMessage()
    {
        return false;
    }
}
