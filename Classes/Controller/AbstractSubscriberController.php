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
use TYPO3\T3extblog\Domain\Model\AbstractSubscriber;

/**
 * SubscriberController
 */
abstract class AbstractSubscriberController extends AbstractController {

	/**
	 * subscriberRepository
	 */
	protected $subscriberRepository;

	/**
	 * subscriber
	 *
	 * @var \TYPO3\T3extblog\Domain\Model\AbstractSubscriber
	 */
	protected $subscriber = NULL;

	/**
	 * feUserService
	 *
	 * @var \TYPO3\T3extblog\Service\AuthenticationServiceInterface
	 * @inject
	 */
	protected $authentication;

	/**
	 * objectManager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Contains the subscription settings
	 *
	 * @var array
	 */
	protected $subscriptionSettings;

	/**
	 * action confirm
	 *
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function confirmAction() {
		$this->checkAuth(TRUE);

		if ($this->subscriber === NULL) {
			throw new \InvalidArgumentException('No authenticated subscriber given.');
		}

		if ($this->subscriber->_getProperty('hidden') === TRUE) {
			$this->subscriber->_setProperty('hidden', FALSE);
			$this->addFlashMessageByKey('confirmed', FlashMessage::OK);

			$this->subscriberRepository->update($this->subscriber);
			$this->persistAllEntities();
		}

		$this->redirect('list', 'PostSubscriber');
	}

	/**
	 * action delete
	 *
	 * @param AbstractSubscriber $subscriber
	 *
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function deleteAction(AbstractSubscriber $subscriber = NULL) {
		$this->checkAuth();

		if ($subscriber === NULL) {
			throw new \InvalidArgumentException('No subscriber given.');
		}

		// Check if the given subscriber is owned by authenticated user
		if ($subscriber->getEmail() !== $this->authentication->getEmail()) {
			throw new \InvalidArgumentException('Invalid subscriber given.');
		}

		$this->subscriberRepository->remove($subscriber);
		$this->persistAllEntities();

		$this->addFlashMessageByKey('deleted', FlashMessage::INFO);
		$this->redirect('list', 'PostSubscriber');
	}

	/**
	 * Check and get authentication
	 *
	 * @param boolean $isConfirmRequest
	 *
	 * @return void
	 */
	protected function checkAuth($isConfirmRequest = FALSE) {
		if ($this->hasCodeArgument()) {
			$this->authenticate($isConfirmRequest);
		}

		if ($this->authentication->isValid()) {
			return;
		}

		$this->forward('processError', 'Subscriber');
	}

	/**
	 * Get authentication
	 *
	 * @param boolean $isConfirmRequest
	 *
	 * @return void
	 */
	protected function authenticate($isConfirmRequest = FALSE) {
		$code = $this->getAuthCode();

		/* @var $subscriber AbstractSubscriber */
		$subscriber = $this->subscriberRepository->findByCode($code, !$isConfirmRequest);

		if ($subscriber === NULL) {
			$this->forward('processError', 'Subscriber', NULL, array('message' => 'authFailed'));
		}

		$modify = '+1 hour';
		if (isset($this->subscriptionSettings['emailHashTimeout'])) {
			$modify = trim($this->subscriptionSettings['emailHashTimeout']);
		}
		if ($subscriber->isAuthCodeExpired($modify)) {
			$this->forward('processError', 'Subscriber', NULL, array('message' => 'linkOutdated'));
		}

		if ($isConfirmRequest === TRUE) {
			$confirmedSubscriptions = $this->findExistingSubscriptions($subscriber);

			if (count($confirmedSubscriptions) > 0) {
				$subscriber->_setProperty('deleted', TRUE);

				$this->subscriberRepository->update($subscriber);
				$this->persistAllEntities();

				$this->forward(
					'processError',
					'Subscriber',
					NULL,
					array('message' => 'alreadyRegistered', 'severity' => FlashMessage::NOTICE)
				);
			}
		}

		$this->authentication->login($subscriber->getEmail());
		$this->subscriber = $subscriber;
	}

	/**
	 * If the request has argument 'code'
	 *
	 * @return string
	 */
	protected function hasCodeArgument() {
		if ($this->request->hasArgument('code') && strlen($this->request->getArgument('code')) > 0) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Checks the code
	 *
	 * @return string
	 */
	protected function getAuthCode() {
		$code = $this->request->getArgument('code');

		if (strlen($code) !== 32 || !ctype_alnum($code)) {
			$this->forward('processError', 'Subscriber', NULL, array('message' => 'invalidLink'));
		}

		return $code;
	}

	/**
	 * Finds existing subscriptions
	 *
	 * @param $subscriber
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	abstract protected function findExistingSubscriptions($subscriber);

}
