<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Felix Nagel <info@felixnagel.com>
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
	 * action list
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post comments related to should be sowed
	 * @return void
	 */
	public function listAction(Tx_T3extblog_Domain_Model_Post $post = NULL) {
		if ($post === NULL) {
			$comments = $this->commentRepository->findAll();
		} else {
			$comments = $this->commentRepository->findByFkPost($post->getUid());
			$this->view->assign('post', $post);
		}

		$this->view->assign('comments', $comments);
	}

	/**
	 * action latest
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post comments related to should be sowed
	 * @return void
	 */
	public function latestAction(Tx_T3extblog_Domain_Model_Post $post = NULL) {
		if ($post === NULL) {
			$comments = $this->commentRepository->findAll();
		} else {
			$comments = $this->commentRepository->findByFkPost($post->getUid());
			$this->view->assign('post', $post);
		}

		$this->view->assign('comments', $comments);
	}


	/**
	 * action new
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment
	 * @ignorevalidation $newComment
	 * @dontvalidate $newComment
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
	 * @todo add spam check
	 * @todo add allowedUnil check
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment The comment to create
	 * @return void
	 */
	public function createAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $newComment) {
		if ($this->settings['blogsystem']['comments']['allowed'] && $post->getAllowComments() === 0) {
			$this->checkIfCommentIsSpam($newComment);

			if ($this->settings['blogsystem']['comments']['approvedByDefault']) {
				$newComment->setApproved(TRUE);
			}

			$post->addComment($newComment);
			$this->notificationService->processAddedComment($newComment);

			$this->addFlashMessage('Created');
		} else {
			$this->addFlashMessage('NotAllowed', t3lib_FlashMessage::ERROR);
		}

		$this->redirect('show', 'Post', NULL, array('post' => $post->getUid(), 'addedComment' => $newComment));
	}

	/**
	 * action edit
	 *
	 * @todo access protection
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return void
	 */
	public function editAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->view->assign('comment', $comment);
		$this->view->assign('post', $post);

		$this->redirect('show', 'Post', NULL, array('post' => $post->getUid(), 'comment' => $comment->getUid()));
	}

	/**
	 * action update
	 *
	 * @todo access protection
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @return void
	 */
	public function updateAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->checkIfCommentIsSpam($comment);

		$this->commentRepository->update($comment);
		$this->notificationService->notifyAdmin($comment);

		$this->addFlashMessage->add('Updated');

		$this->redirect('show', 'Post', NULL, array('post' => $post->getUid(), 'comment' => $comment->getUid()));
	}

	/**
	 * Deletes an existing comment
	 *
	 * @todo access protection
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $comment The comment to be deleted
	 * @return void
	 */
	public function deleteAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $comment) {
		$post->removeComment($comment);
		$this->notificationService->notifyAdmin($comment);

		$this->addFlashMessage('Deleted', t3lib_FlashMessage::INFO);

		$this->redirect('show', 'Post', NULL, array('post' => $post->getUid()));
	}

	/**
	 * Checks comment for SPAM
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment The comment to be deleted
	 * @return void
	 */
	protected function checkIfCommentIsSpam(Tx_T3extblog_Domain_Model_Comment $comment) {
		$spamSettings = $this->settings['blogsystem']['comments']['spamCheck'];
		$spamPoints = 0;

		if (!$spamSettings["enable"]) {
			return;
		}

		if ($spamSettings['honeypot']) {
			if (!$this->checkHoneyPotFields()){
				$spamPoints += intval($spamSettings['honeypot']);
			}
		}

		if ($spamSettings['isHumanCheckbox']) {
			if (!$this->request->hasArgument("human") || !$this->request->hasArgument("human")){
				$spamPoints += intval($spamSettings['isHumanCheckbox']);
			}
		}

		if ($spamSettings['cookie']) {
			if (!$_COOKIE['fe_typo_user']) {
				$spamPoints += intval($spamSettings['cookie']);
			}
		}

		if ($spamSettings['userAgent']) {
			if (t3lib_div::getIndpEnv('HTTP_USER_AGENT') == "") {
				$spamPoints += intval($spamSettings['userAgent']);
			}
		}

		if ($spamSettings['sfpantispam']) {
			if (t3lib_extMgm::isLoaded('sfpantispam')) {
				if ($this->checkCommentWithSfpAntiSpam($comment)) {
					$spamPoints += intval($spamSettings['sfpantispam']);
				}
			} else {
				$this->log->error("EXT:sfpantispam not installed but enabled in configuration.");
			}
		}

		if ($spamPoints > 0) {
			$this->processSpamRequest($comment, $spamPoints);
		}
	}

	/**
	 * Checks comment for SPAM
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment The comment to be deleted
	 * @return void
	 */
	protected function processSpamRequest(Tx_T3extblog_Domain_Model_Comment $comment, $spamPoints) {
		$spamSettings = $this->settings['blogsystem']['comments']['spamCheck']['threshold'];

		// block comment and show message
		if ($spamSettings['block'] > 0 && $spamPoints >= intval($spamSettings['block'])) {
			$this->addFlashMessage('blockedAsSpam', t3lib_FlashMessage::ERROR);
			$this->log->notice("New comment blocked because of SPAM.");
			$this->forward('show', 'Post', NULL, array('post' => $post, 'comment' => $comment));
		}

		// block comment and redirect user
		if ($spamSettings['redirect'] > 0 && $spamPoints >= intval($spamSettings['redirect'])) {
			$this->log->notice("New comment blocked and user redirected because of SPAM.");
			$this->redirect('', NULL, NULL, $settings['redirect']['arguments'], intval($settings['redirect']['pid']), $statusCode = 403);
		}

		// mark as spam
		if ($spamPoints >= intval($spamSettings['markAsSpam'])) {
			$this->addFlashMessage('MarkedAsSpam', t3lib_FlashMessage::INFO);
			$this->log->notice("New comment marked as SPAM.");
			$comment->markAsSpam();
		}
	}

	/**
	 * Checks text fields with EXT:sfpantispam
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return boolean
	 */
	protected function checkCommentWithSfpAntiSpam(Tx_T3extblog_Domain_Model_Comment $comment) {
		/* @var tx_sfpantispam_tslibfepreproc $sfpantispam */
		$sfpantispam = t3lib_div::makeInstance('tx_sfpantispam_tslibfepreproc');
		$fields = array(
			$comment->getAuthor(),
			$comment->getTitle(),
			$comment->getWebsite(),
			$comment->getEmail(),
			$comment->getText()
		);

		return !$sfpantispam->sendFormmail_preProcessVariables($textFields, $this);
	}

	/**
	 * Checks honeypot fields
	 *
	 * @return boolean
	 */
	protected function checkHoneyPotFields() {
		if (!$this->request->hasArgument("author") || strlen($this->request->hasArgument("author")) > 0){
			return FALSE;
		}
		if (!$this->request->hasArgument("link") || strlen($this->request->hasArgument("link")) > 0){
			return FALSE;
		}
		if (!$this->request->hasArgument("text") || strlen($this->request->hasArgument("text")) > 0){
			return FALSE;
		}
		if (!$this->request->hasArgument("timestamp") || $this->request->getArgument("timestamp") !== "1368283172"){
			return FALSE;
		}

		return TRUE;
	}

}
?>