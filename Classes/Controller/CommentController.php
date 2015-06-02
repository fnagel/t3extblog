<?php

/***************************************************************
 *  Copyright notice
 *
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

/**
 *
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Controller_CommentController extends Tx_T3extblog_Controller_AbstractController {

	/**
	 * commentRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_CommentRepository
	 * @inject
	 */
	protected $commentRepository;

	/**
	 * Notification Service
	 *
	 * @var Tx_T3extblog_Service_NotificationService
	 * @inject
	 */
	protected $notificationService;

	/**
	 * Spam Check Service
	 *
	 * @var Tx_T3extblog_Service_SpamCheckService
	 * @inject
	 */
	protected $spamCheckService;

	/**
	 * action list
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post Show only comments related to this post
	 *
	 * @return void
	 */
	public function listAction(Tx_T3extblog_Domain_Model_Post $post = NULL) {
		if ($post === NULL) {
			$comments = $this->commentRepository->findValid();
		} else {
			$comments = $this->commentRepository->findValidByPost($post);
			$this->view->assign('post', $post);
		}

		$this->view->assign('comments', $comments);
	}

	/**
	 * action latest
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post Show only comments related to this post
	 *
	 * @return void
	 */
	public function latestAction(Tx_T3extblog_Domain_Model_Post $post = NULL) {
		$this->listAction($post);
	}

	/**
	 * Show action
	 *
	 * Redirect to post show if empty cmment create is called
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 *
	 * @return void
	 */
	public function showAction(Tx_T3extblog_Domain_Model_Post $post) {
		$this->redirect('show', 'Post', NULL, $post->getLinkParameter());
	}


	/**
	 * action new
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment
	 * @ignorevalidation $newComment
	 * @dontvalidate $newComment
	 *
	 * @return void
	 */
	public function newAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $newComment = NULL) {
		if ($newComment === NULL) {
			$newComment = $this->objectManager->create('Tx_T3extblog_Domain_Model_Comment');
		}

		$this->view->assign('newComment', $newComment);
		$this->view->assign('post', $post);
	}

	/**
	 * Adds a comment to a blog post and redirects to single view
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment The comment to create
	 *
	 * @return void
	 */
	public function createAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $newComment) {
		$this->checkIfCommentIsAllowed($post, $newComment);

		$this->spamCheckService->process($newComment, $this->request);
		$this->checkSpamPoints($newComment, $post);

		$this->sanitizeComment($newComment);

		if ($this->settings['blogsystem']['comments']['approvedByDefault']) {
			$newComment->setApproved(TRUE);
		}

		$post->addComment($newComment);

		/* @var $persistenceManager Tx_Extbase_Persistence_Manager */
		$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');
		$persistenceManager->persistAll();

		$this->notificationService->processCommentAdded($newComment);

		if (!$this->hasFlashMessages()) {
			if ($newComment->isApproved()) {
				$this->addFlashMessageByKey('created', t3lib_FlashMessage::OK);
			} else {
				$this->addFlashMessageByKey('createdUnapproved', t3lib_FlashMessage::NOTICE);
			}
		}

		// clear cache so new comment is displayed
		$this->clearCacheOnError();

		$this->redirect('show', 'Post', NULL, $post->getLinkParameter());
	}

	/**
	 * Checks if a new comment could be created
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment The comment to create
	 *
	 * @return void
	 */
	private function checkIfCommentIsAllowed(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $newComment) {
		$settings = $this->settings['blogsystem']['comments'];

		if (!$settings['allowed'] || $post->getAllowComments() === 1) {
			$this->addFlashMessageByKey('notAllowed', t3lib_FlashMessage::ERROR);
			$this->errorAction();
		}

		if ($post->getAllowComments() === 2 && !(isset($GLOBALS['TSFE']) && $GLOBALS['TSFE']->loginUser)) {
			$this->addFlashMessageByKey('notLoggedIn', t3lib_FlashMessage::ERROR);
			$this->errorAction();
		}

		if ($settings['allowedUntil']) {
			if ($post->isExpired(trim($settings['allowedUntil']))) {
				$this->addFlashMessageByKey('commentsClosed', t3lib_FlashMessage::ERROR);
				$this->errorAction();
			}
		}
	}

	/**
	 * Process comment request
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment The comment to be deleted
	 * @param Tx_T3extblog_Domain_Model_Post $post The comment to be deleted
	 *
	 * @return void
	 */
	protected function checkSpamPoints(Tx_T3extblog_Domain_Model_Comment $comment, Tx_T3extblog_Domain_Model_Post $post) {
		$settings = $this->settings['blogsystem']['comments']['spamCheck'];
		$threshold = $settings['threshold'];
		$logData = array(
			'postUid' => $post->getUid(),
			'spamPoints' => $comment->getSpamPoints(),
		);

		// block comment and redirect user
		if ($threshold['redirect'] > 0 && $comment->getSpamPoints() >= intval($threshold['redirect'])) {
			$this->log->notice('New comment blocked and user redirected because of SPAM.', $logData);
			$this->redirect('', NULL, NULL, $settings['redirect']['arguments'], intval($settings['redirect']['pid']), $statusCode = 403);
		}

		// block comment and show message
		if ($threshold['block'] > 0 && $comment->getSpamPoints() >= intval($threshold['block'])) {
			$this->log->notice('New comment blocked because of SPAM.', $logData);
			$this->addFlashMessageByKey('blockedAsSpam', t3lib_FlashMessage::ERROR);
			$this->errorAction();
		}

		// mark as spam
		if ($comment->getSpamPoints() >= intval($threshold['markAsSpam'])) {
			$this->log->notice('New comment marked as SPAM.', $logData);
			$comment->markAsSpam();
			$this->addFlashMessageByKey('markedAsSpam', t3lib_FlashMessage::NOTICE);
		}
	}

	/**
	 * Sanitize comment content
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 *
	 * @return void
	 */
	protected function sanitizeComment(Tx_T3extblog_Domain_Model_Comment $comment) {
		$allowTags = $this->settings['blogsystem']['comments']['allowTags'];
		$comment->setText(t3lib_div::removeXSS(strip_tags($comment->getText(), trim($allowTags))));
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

?>