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

/**
 * SubscriberController
 */
class SubscriberController extends AbstractController {

	/**
	 * feUserService
	 *
	 * @var \TYPO3\T3extblog\Service\AuthenticationServiceInterface
	 * @inject
	 */
	protected $authentication;

	/**
	 * @var \TYPO3\T3extblog\Domain\Repository\BlogSubscriberRepository
	 * @inject
	 */
	protected $blogSubscriberRepository;

	/**
	 * @var \TYPO3\T3extblog\Domain\Repository\PostSubscriberRepository
	 * @inject
	 */
	protected $postSubscriberRepository;

	/**
	 * Displays a list of all posts a user subscribed to
	 *
	 * @return void
	 */
	public function listAction() {
		if (!$this->authentication->isValid()) {
			$this->forward('list', 'PostSubscriber');
		}

		$email = $this->authentication->getEmail();

		$postSubscriber = $this->postSubscriberRepository->findByEmail($email);
		$blogSubscriber = $this->blogSubscriberRepository->findOneByEmail($email);

		$this->view->assign('email', $email);
		$this->view->assign('postSubscriber', $postSubscriber);
		$this->view->assign('blogSubscriber', $blogSubscriber);
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
	 * Invalidates the auth and redirects user
	 *
	 * @return void
	 */
	public function logoutAction() {
		$this->processErrorAction('logout', FlashMessage::INFO);
	}

	/**
	 * Redirects user when no auth was possible
	 *
	 * @param string $message Flash message key
	 * @param integer $severity Severity code. One of the FlashMessage constants
	 *
	 * @return void
	 */
	protected function processErrorAction($message = 'invalidAuth', $severity = FlashMessage::ERROR) {
		$this->authentication->logout();

		$this->addFlashMessageByKey($message, $severity);
		$this->redirect('error');
	}

	/**
	 * Fallback for old real url confirm configuration
	 *
	 * @todo Remove this with v3.0.0
	 * @deprecated
	 *
	 * @return void
	 */
	public function confirmAction() {
		$this->forward('confirm', 'PostSubscriber');
	}

}
