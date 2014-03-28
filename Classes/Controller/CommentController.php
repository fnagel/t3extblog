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
	 * @param Tx_T3extblog_Domain_Model_Post $post The post comments related to should be sowed
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
	 * @param Tx_T3extblog_Domain_Model_Post $post The post comments related to should be sowed
	 *
	 * @return void
	 */
	public function latestAction(Tx_T3extblog_Domain_Model_Post $post = NULL) {
		$this->listAction($post);
	}

	/**
	 * action new
	 *
	 * @param Tx_T3extblog_Domain_Model_Post    $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment
	 * @ignorevalidation $newComment
	 * @dontvalidate $newComment
	 *
	 * @return void
	 */
	public function newAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $newComment = NULL) {
		if ($newComment === NULL) {
			$newComment = t3lib_div::makeInstance('Tx_T3extblog_Domain_Model_Comment');
		}

		$this->view->assign('newComment', $newComment);
		$this->view->assign('post', $post);
	}

	/**
	 * Adds a comment to a blog post and redirects to single view
	 *
	 * @param Tx_T3extblog_Domain_Model_Post    $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment The comment to create
	 *
	 * @return void
	 */
	public function createAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $newComment) {
		if ($this->checkIfCommentIsAllowed($post, $newComment)) {
			$newComment->setSpamPoints($this->spamCheckService->process($newComment, $this->request));
			$this->processComment($newComment, $post);

			if ($this->settings['blogsystem']['comments']['approvedByDefault']) {
				$newComment->setApproved(TRUE);
			}

			$post->addComment($newComment);

			/* @var $persistenceManager Tx_Extbase_Persistence_Manager */
			$persistenceManager = t3lib_div::makeInstance('Tx_Extbase_Persistence_Manager');
			$persistenceManager->persistAll();

			$this->notificationService->processCommentAdded($newComment);

			if (!$this->hasFlashMessages()) {
				$this->addFlashMessage('Created', t3lib_FlashMessage::OK);
			}
		}

		$this->redirect('show', 'Post', NULL, $post->getLinkParameter());
	}

	/**
	 * action edit
	 *
	 * @todo access protection
	 *
	 * @param Tx_T3extblog_Domain_Model_Post    $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 *
	 * @return void
	 */
	public function editAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->view->assign('comment', $comment);
		$this->view->assign('post', $post);

		$this->redirect('show', 'Post', NULL, array('post' => $post, 'comment' => $comment));
	}

	/**
	 * action update
	 *
	 * @todo access protection
	 *
	 * @param Tx_T3extblog_Domain_Model_Post    $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 *
	 * @return void
	 */
	public function updateAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $comment) {
		$comment->setSpamPoints($this->spamCheckService->process($comment, $this->request));
		$this->processComment($comment, $post);

		if ($this->settings['blogsystem']['comments']['approvedByDefault']) {
			$comment->setApproved(TRUE);
		} else {
			$comment->setApproved(FALSE);
		}

		$this->commentRepository->update($comment);
		if (!$this->hasFlashMessages()) {
			$this->addFlashMessage('Updated');
		}

		$this->redirect('show', 'Post', NULL, array('post' => $post, 'comment' => $comment));
	}

	/**
	 * Deletes an existing comment
	 *
	 * @todo access protection
	 *
	 * @param Tx_T3extblog_Domain_Model_Post    $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $comment The comment to be deleted
	 *
	 * @return void
	 */
	public function deleteAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $comment) {
		$post->removeComment($comment);

		$this->addFlashMessage('Deleted', t3lib_FlashMessage::INFO);

		$this->redirect('list', 'Post');
	}


	/**
	 * Checks if a new comment could be created
	 *
	 * @param Tx_T3extblog_Domain_Model_Post    $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment The comment to create
	 *
	 * @return boolean If the comment should be saved
	 */
	private function checkIfCommentIsAllowed(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $newComment) {
		$settings = $this->settings['blogsystem']['comments'];

		if (!($settings['allowed'] && $post->getAllowComments() === 0)) {
			$this->addFlashMessage('NotAllowed', t3lib_FlashMessage::ERROR);
			return FALSE;
		}

		if ($settings["allowedUntil"]) {
			if ($post->isExpired(trim($settings["allowedUntil"]))) {
				$this->addFlashMessage('CommentsClosed', t3lib_FlashMessage::ERROR);
				return FALSE;
			}
		}

		return TRUE;
	}

	/**
	 * Process comment request
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment The comment to be deleted
	 * @param Tx_T3extblog_Domain_Model_Post $post The comment to be deleted
	 */
	protected function processComment(Tx_T3extblog_Domain_Model_Comment $comment, Tx_T3extblog_Domain_Model_Post $post) {
		$settings = $this->settings['blogsystem']['comments']['spamCheck'];
		$allowTags = $this->settings['blogsystem']['comments']['allowTags'];
		$threshold = $settings['threshold'];
		$logData = array(
			'postUid' => $post->getUid(),
			'spamPoints' => $comment->getSpamPoints(),
		);

		// Sanitize comment
		$comment->setText(strip_tags($comment->getText(), trim($allowTags)));

		// block comment and redirect user
		if ($threshold['redirect'] > 0 && $comment->getSpamPoints() >= intval($threshold['redirect'])) {
			$this->log->notice("New comment blocked and user redirected because of SPAM.", $logData);
			$this->redirect('', NULL, NULL, $settings['redirect']['arguments'], intval($settings['redirect']['pid']), $statusCode = 403);
		}

		// block comment and show message
		if ($threshold['block'] > 0 && $comment->getSpamPoints() >= intval($threshold['block'])) {
			$this->addFlashMessage('blockedAsSpam', t3lib_FlashMessage::ERROR);
			$this->log->notice("New comment blocked because of SPAM.", $logData);
			$this->forward('show', 'Post', NULL, array('post' => $post, 'newComment' => $comment));
		}

		// mark as spam
		if ($comment->getSpamPoints() >= intval($threshold['markAsSpam'])) {
			$this->addFlashMessage('MarkedAsSpam', t3lib_FlashMessage::INFO);
			$this->log->notice("New comment marked as SPAM.", $logData);
			$comment->markAsSpam();
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

?>