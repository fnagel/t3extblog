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
use FelixNagel\T3extblog\Utility\FrontendUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;

/**
 * BlogSubscriberFormController.
 */
class BlogSubscriberFormController extends AbstractController
{
    /**
     * @var BlogSubscriberRepository
     */
    protected $blogSubscriberRepository;

    /**
     * @var BlogNotificationService
     */
    protected $notificationService;

    /**
     * @var SpamCheckServiceInterface
     */
    protected $spamCheckService;

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
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("subscriber")
     */
    public function newAction(BlogSubscriber $subscriber = null): ResponseInterface
    {
        /* @var $subscriber BlogSubscriber */
        if ($subscriber === null) {
            $subscriber = $this->objectManager->get(BlogSubscriber::class);
        }

        $this->view->assign('subscriber', $subscriber);

        return $this->htmlResponse();
    }

    /**
     * Adds a subscriber.
     */
    public function createAction(BlogSubscriber $subscriber = null): ResponseInterface
    {
        if ($subscriber === null) {
            $this->redirect('new');
        }

        // Check if blog subscription is allowed
        if (!$this->settings['blogSubscription']['subscribeForPosts']) {
            $this->addFlashMessageByKey('notAllowed', FlashMessage::ERROR);
            return $this->errorAction();
        }

        $spamPointResult = $this->checkSpamPoints();
        if ($spamPointResult instanceof ResponseInterface) {
            return $spamPointResult;
        }

        // Check if user already registered
        $subscribers = $this->blogSubscriberRepository->findExistingSubscriptions($subscriber->getEmail());
        if (count($subscribers) > 0) {
            $this->addFlashMessageByKey('alreadyRegistered', FlashMessage::INFO);
            return $this->errorAction();
        }

        $subscriber->setSysLanguageUid(FrontendUtility::getLanguageUid());

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
    protected function checkSpamPoints(): ?ResponseInterface
    {
        $settings = $this->settings['blogSubscription']['spamCheck'];
        $threshold = $settings['threshold'];

        $spamPoints = $this->spamCheckService->process($settings);
        $logData = ['spamPoints' => $spamPoints];

        // Block comment and redirect user
        if ($threshold['redirect'] > 0 && $spamPoints >= (int) $threshold['redirect']) {
            $this->getLog()->notice('New blog subscriber blocked and user redirected because of SPAM.', $logData);
            $this->redirect(
                '',
                null,
                null,
                $settings['redirect']['arguments'],
                (int)$settings['redirect']['pid']
            );
        }

        // Block comment and show message
        if ($threshold['block'] > 0 && $spamPoints >= (int) $threshold['block']) {
            $this->getLog()->notice('New blog subscriber blocked because of SPAM.', $logData);
            $this->addFlashMessageByKey('blockedAsSpam', FlashMessage::ERROR);
            return $this->errorAction();
        }

        return null;
    }

    public function successAction()
    {
        if (!$this->hasFlashMessages()) {
            $this->redirect('new');
        }
    }

    /**
     * Disable error flash message.
     */
    protected function getErrorFlashMessage()
    {
        return false;
    }
}
