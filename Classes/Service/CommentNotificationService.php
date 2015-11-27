<?php

namespace TYPO3\T3extblog\Service;

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

use TYPO3\T3extblog\Domain\Model\Post;
use TYPO3\T3extblog\Domain\Model\Comment;
use TYPO3\T3extblog\Domain\Model\PostSubscriber;

/**
 * Handles all notification mails for new or changed comments
 */
class CommentNotificationService extends AbstractNotificationService {

	/**
	 * subscriberRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\PostSubscriberRepository
	 * @inject
	 */
	protected $subscriberRepository;

	/**
	 * @return void
	 */
	public function initializeObject() {
		parent::initializeObject();

		$this->subscriptionSettings = $this->settingsService->getTypoScriptByPath('subscriptionManager.comment');
	}

	/**
	 * Process added comment
	 * Comment is already persisted to DB
	 *
	 * @param Comment $comment Comment
	 *
	 * @return void
	 */
	public function processNewEntity($comment) {
		if (!($comment instanceof Comment)) {
			throw new \InvalidArgumentException('Object should be of type Comment!');
		}

		$subscriber = NULL;
		if ($this->isNewSubscriptionValid($comment)) {
			$subscriber = $this->addSubscriber($comment);
		}

		if ($comment->isValid()) {
			if ($subscriber instanceof PostSubscriber) {
				$this->sendOptInMail($subscriber, $comment);
			}

			$this->notifySubscribers($comment);

			$this->persistToDatabase();
			$this->flushFrontendCache();
		}

	}

	/**
	 * Process changed status of a comment
	 * Comment is already persisted to DB
	 *
	 * @param Comment $comment Comment
	 *
	 * @return void
	 */
	public function processChangedStatus($comment) {
		if (!($comment instanceof Comment)) {
			throw new \InvalidArgumentException('Object should be of type Comment!');
		}

		if ($comment->isValid()) {
			$subscriber = $this->subscriberRepository->findForSubscriptionMail($comment);
			if ($subscriber instanceof PostSubscriber) {
				$this->sendOptInMail($subscriber, $comment);
			}

			$this->notifySubscribers($comment);

			$this->persistToDatabase();
			$this->flushFrontendCache();
		}
	}

	/**
	 * Checks if a new subscription should be added
	 *
	 * @param Comment $comment
	 *
	 * @return boolean
	 */
	protected function isNewSubscriptionValid(Comment $comment) {
		if (!$this->settings['blogsystem']['comments']['subscribeForComments'] || !$comment->getSubscribe()) {
			return FALSE;
		}

		// check if user already registered
		$subscribers = $this->subscriberRepository->findExistingSubscriptions(
			$comment->getPostId(), $comment->getEmail()
		);
		if (count($subscribers) > 0) {
			$this->log->notice('Subscriber [' . $comment->getEmail() . '] already registered.');
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Send optin mail for subscirber
	 *
	 * @param PostSubscriber $subscriber
	 * @param Comment $comment Comment
	 *
	 * @return void
	 */
	protected function sendOptInMail(PostSubscriber $subscriber, Comment $comment) {
		$this->log->dev('Send subscriber opt-in mail.');

		$post = $subscriber->getPost();

		$subscriber->updateAuth();
		$this->subscriberRepository->update($subscriber);

		$this->sendEmail(
			$subscriber,
			$this->translate('subject.subscriber.new', $post->getTitle()),
			$this->subscriptionSettings['subscriber']['template']['confirm'],
			array(
				'post' => $post,
				'comment' => $comment,
			)
		);
	}

	/**
	 * Add a subscriber
	 *
	 * @param Comment $comment
	 *
	 * @return PostSubscriber
	 */
	protected function addSubscriber(Comment $comment) {
		/* @var $newSubscriber PostSubscriber */
		$newSubscriber = $this->objectManager->get(
			'TYPO3\\T3extblog\\Domain\\Model\\PostSubscriber', $comment->getPostId()
		);
		$newSubscriber->setEmail($comment->getEmail());
		$newSubscriber->setName($comment->getAuthor());

		$this->subscriberRepository->add($newSubscriber);
		$this->persistToDatabase(TRUE);

		$this->log->dev('Added comment subscriber uid=' . $newSubscriber->getUid());

		return $newSubscriber;
	}

	/**
	 * Send comment notification mails
	 *
	 * @param Comment $comment
	 *
	 * @return    void
	 */
	protected function notifySubscribers(Comment $comment) {
		$settings = $this->subscriptionSettings['subscriber'];

		if (!$settings['enableNotifications']) {
			return;
		}

		if ($comment->getMailsSent()) {
			return;
		}

		/* @var $post Post */
		$post = $comment->getPost();
		$subscribers = $this->subscriberRepository->findForNotification($post);
		$subject = $this->translate('subject.subscriber.notify', $post->getTitle());
		$variables = array(
			'post' => $post,
			'comment' => $comment,
		);

		$this->log->dev('Send post subscriber notification mails to ' . count($subscribers) . ' users.');

		/* @var $subscriber PostSubscriber */
		foreach ($subscribers as $subscriber) {
			// make sure we do not notify the author of the triggering comment
			if ($comment->getEmail() === $subscriber->getEmail()) {
				continue;
			}

			$subscriber->updateAuth();
			$this->subscriberRepository->update($subscriber);

			$this->sendEmail(
				$subscriber, $subject, $settings['template']['notification'], $variables
			);
		}

		$comment->setMailsSent(TRUE);
		$this->objectManager->get('TYPO3\\T3extblog\\Domain\\Repository\\CommentRepository')->update($comment);
	}

	/**
	 * Notify the blog admin
	 *
	 * @param Comment $comment
	 *
	 * @return    void
	 */
	public function notifyAdmin(Comment $comment) {
		$settings = $this->subscriptionSettings['admin'];

		if (!$settings['enableNotifications']) {
			return;
		}

		if (!(is_array($settings['mailTo']) && strlen($settings['mailTo']['email']) > 0)) {
			$this->log->error('No admin email configured.', $settings['mailTo']);
			return;
		}

		$this->log->dev('Send admin new comment notification mail.');

		/* @var $post Post */
		$post = $comment->getPost();
		$subject = $this->translate('subject.admin.newSubscription', $post->getTitle());
		$variables = array(
			'post' => $post,
			'comment' => $comment,
			'subject' => $subject
		);
		$emailBody = $this->emailService->render($variables, $settings['template']);

		$this->emailService->send(
			array($settings['mailTo']['email'] => $settings['mailTo']['name']),
			array($settings['mailFrom']['email'] => $settings['mailFrom']['name']),
			$subject,
			$emailBody
		);
	}

}
