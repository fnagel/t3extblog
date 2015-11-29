<?php

namespace TYPO3\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Felix Kopp <felix-source@phorax.com>
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\T3extblog\Domain\Model\Post;

/**
 * BackendPostController
 */
class BackendPostController extends BackendBaseController {

	/**
	 * Main Backendmodule: displays posts and pending comments
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assignMultiple(array(
			'posts' => $this->postRepository->findByPage($this->pageId, FALSE),
			'pendingComments' => $this->commentRepository->findPendingByPage($this->pageId)
		));
	}
	/**
	 * Send post notification mails
	 *
	 * @param \TYPO3\T3extblog\Domain\Model\Post $post
	 *
	 * @return void
	 */
	public function sendPostNotificationsAction(Post $post) {
		/* @var $notificationService \TYPO3\T3extblog\Service\BlogNotificationService */
		$notificationService = $this->objectManager->get('TYPO3\\T3extblog\\Service\\BlogNotificationService');
		$amount = $notificationService->notifySubscribers($post);

		$this->addFlashMessage(LocalizationUtility::translate('module.post.emailsSent', 'T3extblog', array($amount)));

		$this->redirect('index');
	}

}