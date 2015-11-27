<?php

namespace TYPO3\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Felix Nagel <info@felixnagel.com>
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
use TYPO3\T3extblog\Domain\Model\BlogSubscriber;

/**
 * BlogSubscriberFormController
 */
class BlogSubscriberFormController extends AbstractController {

	/**
	 * blogSubscriberRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\BlogSubscriberRepository
	 * @inject
	 */
	protected $blogSubscriberRepository;

	/**
	 * Notification Service
	 *
	 * @var \TYPO3\T3extblog\Service\BlogNotificationService
	 * @inject
	 */
	protected $notificationService;

	/**
	 * action new
	 *
	 * @todo Auto fill email field when authenticated
	 *
	 * @param \TYPO3\T3extblog\Domain\Model\BlogSubscriber $subscriber
	 * @ignorevalidation $subscriber
	 * @return void
	 */
	public function newAction(BlogSubscriber $subscriber = NULL) {
		/* @var $subscriber \TYPO3\T3extblog\Domain\Model\BlogSubscriber */
		if ($subscriber === NULL) {
			$subscriber = $this->objectManager->get('TYPO3\\T3extblog\\Domain\\Model\\BlogSubscriber');
		}

		$this->view->assign('subscriber', $subscriber);
	}

	/**
	 * Adds a subscriber
	 *
	 * @todo Add SPAM check service
	 *
	 * @param \TYPO3\T3extblog\Domain\Model\BlogSubscriber $subscriber
	 * @return void
	 */
	public function createAction(BlogSubscriber $subscriber = NULL) {
		if ($subscriber === NULL) {
			$this->redirect('new');
		}

		if (!$this->settings['subscriptionManager']['blog']['subscribeForPosts']) {
			$this->addFlashMessageByKey('notAllowed', FlashMessage::ERROR);
			$this->errorAction();
		}

		// check if user already registered
		$subscribers = $this->blogSubscriberRepository->findExistingSubscriptions($subscriber->getEmail());
		if (count($subscribers) > 0) {
			$this->addFlashMessageByKey('alreadySubscribed', FlashMessage::INFO);
			$this->errorAction();
		}

		$subscriber->_setProperty('sys_laguage_uid', (int) $GLOBALS['TSFE']->sys_language_uid);

		$this->blogSubscriberRepository->add($subscriber);
		$this->persistAllEntities();
		$this->log->dev('Added blog subscriber uid=' . $subscriber->getUid());

		$this->notificationService->processNewEntity($subscriber);

		$this->addFlashMessageByKey('success');
		$this->redirect('success');
	}

	/**
	 * action success
	 *
	 * @return void
	 */
	public function successAction() {
		if (!$this->hasFlashMessages()) {
			$this->redirect('new');
		}
	}

	/**
	 * Disable error flash message
	 *
	 * @return string|boolean
	 */
	protected function getErrorFlashMessage() {
		return FALSE;
	}

}