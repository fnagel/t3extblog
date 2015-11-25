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
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception
	 * @return void
	 */
	public function confirmAction() {
		$this->checkAuth(TRUE);

		if ($this->subscriber === NULL) {
			throw new MvcException('No authenticated subscriber given.');
		}

		if ($this->subscriber->_getProperty('hidden') === TRUE) {
			$this->subscriber->_setProperty('hidden', FALSE);
			$this->addFlashMessageByKey('confirmed', FlashMessage::NOTICE);

			$this->subscriberRepository->update($this->subscriber);
			$this->persistAllEntities();
		}

		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param AbstractSubscriber $subscriber
	 *
	 * @throws InvalidArgumentValueException
	 * @return void
	 */
	public function deleteAction(AbstractSubscriber $subscriber = NULL) {
		$this->checkAuth();

		if ($subscriber === NULL) {
			throw new InvalidArgumentValueException('No subscriber given.');
		}

		// Check if the given subscriber is owned by authenticated user
		if ($subscriber->getEmail() !== $this->authentication->getEmail()) {
			throw new \InvalidArgumentException('Invalid subscriber given.');
		}

		$this->subscriberRepository->remove($subscriber);
		$this->persistAllEntities();

		$this->addFlashMessageByKey('deleted', FlashMessage::NOTICE);
		$this->redirect('list');
	}

	/**
	 * Invalidates the auth and redirects user
	 *
	 * @return void
	 */
	protected function logoutAction() {
		$this->processError('logout', FlashMessage::NOTICE);
	}

	/**
	 * Error action
	 *
	 * @return void
	 */
	public function errorAction() {
		if (!$this->hasFlashMessages()) {
			$this->addFlashMessageByKey('invalidAuth', FlashMessage::ERROR);
		}
	}

	/**
	 * Redirects user when no auth was possible
	 *
	 * @param string $message Flash message key
	 * @param integer $severity Severity code. One of the FlashMessage constants
	 *
	 * @return void
	 */
	protected function processError($message = 'invalidAuth', $severity = FlashMessage::ERROR) {
		$this->authentication->logout();

		$this->addFlashMessageByKey($message, $severity);
		$this->redirect('error');
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

		$this->processError();
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
			$this->processError('authFailed');
		}

		$modify = '+1 hour';
		if (isset($this->subscriptionSettings['emailHashTimeout'])) {
			$modify = trim($this->subscriptionSettings['emailHashTimeout']);
		}
		if ($subscriber->isAuthCodeExpired($modify)) {
			$this->processError('linkOutdated');
		}

		if ($isConfirmRequest === TRUE) {
			$confirmedSubscriptions = $this->findExistingSubscriptions($subscriber);

			if (count($confirmedSubscriptions) > 0) {
				$subscriber->_setProperty('deleted', TRUE);

				$this->subscriberRepository->update($subscriber);
				$this->persistAllEntities();

				$this->processError('alreadyRegistered', FlashMessage::NOTICE);
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
			$this->processError('invalidLink');
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
