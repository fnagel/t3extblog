<?php

namespace FelixNagel\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2018 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

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
     * @var \FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $blogSubscriberRepository;

    /**
     * Notification Service.
     *
     * @var \FelixNagel\T3extblog\Service\BlogNotificationService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $notificationService;

    /**
     * Spam Check Service.
     *
     * @var \FelixNagel\T3extblog\Service\SpamCheckServiceInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $spamCheckService;

    /**
     * action new.
     *
     * @param BlogSubscriber $subscriber
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $subscriber
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
        $this->log->dev('Added blog subscriber uid='.$subscriber->getUid());

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
            $this->log->notice('New blog subscriber blocked and user redirected because of SPAM.', $logData);
            $this->redirect('', null, null, $settings['redirect']['arguments'], intval($settings['redirect']['pid']), $statusCode = 403);
        }

        // block comment and show message
        if ($threshold['block'] > 0 && $spamPoints >= intval($threshold['block'])) {
            $this->log->notice('New blog subscriber blocked because of SPAM.', $logData);
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
