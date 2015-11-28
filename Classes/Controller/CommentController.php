<?php

namespace TYPO3\T3extblog\Controller;

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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\T3extblog\Domain\Model\Comment;
use TYPO3\T3extblog\Domain\Model\Post;

/**
 * CommentController
 */
class CommentController extends AbstractController {

	/**
	 * commentRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\CommentRepository
	 * @inject
	 */
	protected $commentRepository;

	/**
	 * Notification Service
	 *
	 * @var \TYPO3\T3extblog\Service\CommentNotificationService
	 * @inject
	 */
	protected $notificationService;

	/**
	 * Spam Check Service
	 *
	 * @var \TYPO3\T3extblog\Service\SpamCheckService
	 * @inject
	 */
	protected $spamCheckService;

	/**
	 * action list
	 *
	 * @param Post $post Show only comments related to this post
	 *
	 * @return void
	 */
	public function listAction(Post $post = NULL) {
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
	 * @param Post $post Show only comments related to this post
	 *
	 * @return void
	 */
	public function latestAction(Post $post = NULL) {
		$this->listAction($post);
	}

	/**
	 * Show action
	 *
	 * Redirect to post show if empty cmment create is called
	 *
	 * @param Post $post The post the comment is related to
	 *
	 * @return void
	 */
	public function showAction(Post $post) {
		$this->redirect('show', 'Post', NULL, $post->getLinkParameter());
	}


	/**
	 * action new
	 *
	 * @param Post $post The post the comment is related to
	 * @param Comment $newComment
	 * @ignorevalidation $newComment
	 * @dontvalidate $newComment
	 *
	 * @return void
	 */
	public function newAction(Post $post, Comment $newComment = NULL) {
		if ($newComment === NULL) {
			$newComment = $this->objectManager->get('TYPO3\\T3extblog\\Domain\\Model\\Comment');
		}

		$this->view->assign('newComment', $newComment);
		$this->view->assign('post', $post);
	}

	/**
	 * Adds a comment to a blog post and redirects to single view
	 *
	 * @param Post $post The post the comment is related to
	 * @param Comment $newComment The comment to create
	 *
	 * @return void
	 */
	public function createAction(Post $post, Comment $newComment) {
		$this->checkIfCommentIsAllowed($post);
		$this->checkSpamPoints($newComment);
		$this->sanitizeComment($newComment);

		if ($this->settings['blogsystem']['comments']['approvedByDefault']) {
			$newComment->setApproved(TRUE);
		}

		$post->addComment($newComment);
		$this->persistAllEntities();

		$this->notificationService->processNewEntity($newComment);
		$this->notificationService->notifyAdmin($newComment);

		if (!$this->hasFlashMessages()) {
			if ($newComment->isApproved()) {
				$this->addFlashMessageByKey('created', FlashMessage::OK);
			} else {
				$this->addFlashMessageByKey('createdUnapproved', FlashMessage::NOTICE);
			}
		}

		// clear cache so new comment is displayed
		$this->clearCacheOnError();

		$this->redirect('show', 'Post', NULL, $post->getLinkParameter());
	}

	/**
	 * Checks if a new comment could be created
	 *
	 * @param Post $post The post the comment is related to
	 *
	 * @return void
	 */
	protected function checkIfCommentIsAllowed(Post $post) {
		$settings = $this->settings['blogsystem']['comments'];

		if (!$settings['allowed'] || $post->getAllowComments() === 1) {
			$this->addFlashMessageByKey('notAllowed', FlashMessage::ERROR);
			$this->errorAction();
		}

		if ($post->getAllowComments() === 2 && empty(\TYPO3\T3extblog\Utility\GeneralUtility::getTsFe()->loginUser)) {
			$this->addFlashMessageByKey('notLoggedIn', FlashMessage::ERROR);
			$this->errorAction();
		}

		if ($settings['allowedUntil']) {
			if ($post->isExpired(trim($settings['allowedUntil']))) {
				$this->addFlashMessageByKey('commentsClosed', FlashMessage::ERROR);
				$this->errorAction();
			}
		}
	}

	/**
	 * Process comment request
	 *
	 * @param Comment $comment The comment to be deleted
	 *
	 * @return void
	 */
	protected function checkSpamPoints(Comment $comment) {
		$settings = $this->settings['blogsystem']['comments']['spamCheck'];
		$comment->setSpamPoints($this->spamCheckService->process($settings));

		$threshold = $settings['threshold'];
		$logData = array(
			'commentUid' => $comment->getUid(),
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
			$this->addFlashMessageByKey('blockedAsSpam', FlashMessage::ERROR);
			$this->errorAction();
		}

		// mark as spam
		if ($comment->getSpamPoints() >= intval($threshold['markAsSpam'])) {
			$this->log->notice('New comment marked as SPAM.', $logData);
			$comment->markAsSpam();
			$this->addFlashMessageByKey('markedAsSpam', FlashMessage::NOTICE);
		}
	}

	/**
	 * Sanitize comment content
	 *
	 * @param Comment $comment
	 *
	 * @return void
	 */
	protected function sanitizeComment(Comment $comment) {
		$allowTags = $this->settings['blogsystem']['comments']['allowTags'];
		$comment->setText(GeneralUtility::removeXSS(strip_tags($comment->getText(), trim($allowTags))));
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
