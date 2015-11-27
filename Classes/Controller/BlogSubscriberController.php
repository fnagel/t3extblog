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
use TYPO3\CMS\Extbase\Mvc\Exception as MvcException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentValueException;
use TYPO3\T3extblog\Domain\Model\BlogSubscriber;

/**
 * BlogSubscriberController
 */
class BlogSubscriberController extends AbstractSubscriberController {

	/**
	 * subscriberRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\BlogSubscriberRepository
	 * @inject
	 */
	protected $subscriberRepository;

	/**
	 * Notification Service
	 *
	 * @var \TYPO3\T3extblog\Service\BlogNotificationService
	 * @inject
	 */
	protected $notificationService;

	/**
	 * subscriber
	 *
	 * @var \TYPO3\T3extblog\Domain\Model\PostSubscriber
	 */
	protected $subscriber = NULL;

	/**
	 * @inheritdoc
	 *
	 * @return void
	 */
	protected function initializeAction() {
		parent::initializeAction();

		$this->subscriptionSettings = $this->settings['subscriptionManager']['blog']['subscriber'];
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$this->checkAuth();

		$this->redirect('list', 'Subscriber');
	}

	/**
	 * Displays a form (create) or a button (delete)
	 *
	 * @return void
	 */
	public function createAction() {
		$this->checkAuth();
		$email = $this->authentication->getEmail();

		if (!$this->settings['subscriptionManager']['blog']['subscribeForPosts']) {
			$this->addFlashMessageByKey('notAllowed', FlashMessage::ERROR);
			$this->redirect('list', 'PostSubscriber');
		}

		// check if user already registered
		$subscribers = $this->subscriberRepository->findExistingSubscriptions($email);
		if (count($subscribers) > 0) {
			$this->addFlashMessageByKey('alreadySubscribed', FlashMessage::NOTICE);
			$this->redirect('list', 'PostSubscriber');
		}

		/* @var $subscriber \TYPO3\T3extblog\Domain\Model\BlogSubscriber */
		$subscriber = $this->objectManager->get('TYPO3\\T3extblog\\Domain\\Model\\BlogSubscriber');
		$subscriber->setEmail($email);
		$subscriber->setHidden(FALSE);
		$subscriber->setSysLanguageUid((int) $GLOBALS['TSFE']->sys_language_uid);

		$this->subscriberRepository->add($subscriber);
		$this->persistAllEntities();
		$this->log->dev('Added blog subscriber uid=' . $subscriber->getUid());

		$this->notificationService->processNewEntity($subscriber);

		$this->addFlashMessageByKey('created');
		$this->redirect('list', 'PostSubscriber');
	}

	/**
	 * action delete
	 *
	 * @param BlogSubscriber $subscriber
	 *
	 * @throws InvalidArgumentValueException
	 * @return void
	 */
	public function deleteAction(BlogSubscriber $subscriber = NULL) {
		parent::deleteAction($subscriber);
	}

	/**
	 * Finds existing subscriptions
	 *
	 * @param BlogSubscriber $subscriber
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	protected function findExistingSubscriptions($subscriber) {
		return $this->subscriberRepository->findExistingSubscriptions(
			$subscriber->getEmail(),
			$subscriber->getUid()
		);
	}

}
