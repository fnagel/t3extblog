<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2014 Felix Nagel <info@felixnagel.com>
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

/**
 *
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Controller_SubscriberController extends Tx_T3extblog_Controller_AbstractController {

	/**
	 * subscriberRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_SubscriberRepository
	 * @inject
	 */
	protected $subscriberRepository;

	/**
	 * subscriber
	 *
	 * @var Tx_T3extblog_Domain_Model_Subscriber
	 */
	protected $subscriber = NULL;

	/**
	 * feUserService
	 *
	 * @var Tx_T3extblog_Service_AuthenticationServiceInterface
	 * @inject
	 */
	protected $authentication;

	/**
	 * objectManager
	 *
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * Displays a list of all posts a user subscribed to
	 *
	 * @return void
	 */
	public function listAction() {
		$this->checkAuth();

		$email = $this->authentication->getEmail();
		$subscribers = $this->subscriberRepository->findByEmail($email);

		$this->view->assign('email', $email);
		$this->view->assign('subscribers', $subscribers);
	}

	/**
	 * action confirm
	 *
	 * @throws Tx_Extbase_MVC_Exception
	 * @return void
	 */
	public function confirmAction() {
		$this->checkAuth(TRUE);

		if ($this->subscriber === NULL) {
			throw new Tx_Extbase_MVC_Exception('No authenticated subscriber given.');
		}

		if ($this->subscriber->_getProperty('hidden') === TRUE) {
			$this->subscriber->_setProperty('hidden', FALSE);
			$this->addFlashMessage('confirmed', t3lib_FlashMessage::NOTICE);
		}

		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscriber
	 *
	 * @throws Tx_Extbase_MVC_Exception_InvalidArgumentValue
	 * @return void
	 */
	public function deleteAction(Tx_T3extblog_Domain_Model_Subscriber $subscriber = NULL) {
		$this->checkAuth();

		if ($subscriber === NULL) {
			throw new Tx_Extbase_MVC_Exception_InvalidArgumentValue('No subscriber given.');
		}

		$this->subscriberRepository->remove($subscriber);
		$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();

		$this->addFlashMessage('deleted', t3lib_FlashMessage::NOTICE);
		$this->redirect('list');
	}

	/**
	 * Invalidates the auth and redirects user
	 *
	 * @return void
	 */
	protected function logoutAction() {
		$this->processError('logout', t3lib_FlashMessage::NOTICE);
	}

	/**
	 * Error action
	 *
	 * @return void
	 */
	public function errorAction() {
		if (!$this->hasFlashMessages()) {
			$this->addFlashMessage('invalidAuth', t3lib_FlashMessage::ERROR);
		}
	}

	/**
	 * Redirects user when no auth was possible
	 *
	 * @param string $message Flash message key
	 * @param integer $severity optional severity code. One of the t3lib_FlashMessage constants
	 *
	 * @return void
	 */
	protected function processError($message = 'invalidAuth', $severity = t3lib_FlashMessage::ERROR) {
		$this->authentication->logout();

		$this->addFlashMessage($message, $severity);
		$this->redirect("error");
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

		/* @var $subscriber Tx_T3extblog_Domain_Model_Subscriber */
		$subscriber = $this->subscriberRepository->findByCode($code, !$isConfirmRequest);

		if ($subscriber === NULL) {
			$this->processError('authFailed');
		}

		if ($subscriber->isAuthCodeExpired(trim($this->settings["subscriptionManager"]["subscriber"]["emailHashTimeout"]))) {
			$this->processError('linkOutdated');
		}

		if ($isConfirmRequest === TRUE) {
			$confirmedSubscriptions = $this->subscriberRepository->findExistingSubscriptions(
				$subscriber->getPostUid(),
				$subscriber->getEmail(),
				$subscriber->getUid()
			);

			if (count($confirmedSubscriptions) > 0) {
				$subscriber->_setProperty('deleted', TRUE);
				$this->processError('alreadyRegistered', t3lib_FlashMessage::NOTICE);
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
		if ($this->request->hasArgument("code") && strlen($this->request->getArgument("code")) > 0) {
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
		$code = $this->request->getArgument("code");

		if (strlen($code) !== 32 || !ctype_alnum($code)) {
			$this->processError('invalidLink');
		}

		return $code;
	}

}

?>