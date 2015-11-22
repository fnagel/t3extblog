<?php

namespace TYPO3\T3extblog\Service;

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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\T3extblog\Domain\Model\Post;
use TYPO3\T3extblog\Domain\Model\Comment;
use TYPO3\T3extblog\Domain\Model\PostSubscriber;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Handles all notification mails
 */
class NotificationService implements NotificationServiceInterface, SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * subscriberRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\PostSubscriberRepository
	 * @inject
	 */
	protected $subscriberRepository;

	/**
	 * Logging Service
	 *
	 * @var \TYPO3\T3extblog\Service\LoggingService
	 * @inject
	 */
	protected $log;

	/**
	 * @var \TYPO3\T3extblog\Service\SettingsService
	 * @inject
	 */
	protected $settingsService;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var \TYPO3\T3extblog\Service\EmailService
	 * @inject
	 */
	protected $emailService;

	/**
	 * @var \TYPO3\CMS\Extbase\Service\CacheService
	 * @inject
	 */
	protected $cacheService;

	/**
	 * @return void
	 */
	public function initializeObject() {
		$this->settings = $this->settingsService->getTypoScriptSettings();
	}

	/**
	 * Process added comment
	 * Comment is already persisted to DB
	 *
	 * @param Comment $comment Comment
	 * @param boolean $notifyAdmin
	 *
	 * @return void
	 */
	public function processCommentAdded(Comment $comment, $notifyAdmin = TRUE) {
		if ($notifyAdmin === TRUE) {
			$this->notifyAdmin($comment);
		}

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
	public function processCommentStatusChanged(Comment $comment) {
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

		$subject = $this->translate('subject.subscriber.new', $post->getTitle());
		$variables = array(
			'post' => $post,
			'comment' => $comment,
			'subscriber' => $subscriber,
			'subject' => $subject,
			'validUntil' => $this->getValidUntil()
		);
		$emailBody = $this->emailService->render($variables, 'SubscriberOptinMail.txt');

		$this->emailService->send(
			$subscriber->getMailTo(),
			$this->settings['subscriptionManager']['subscriber']['mailFrom'],
			$subject,
			$emailBody
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
		$newSubscriber = $this->objectManager->get('TYPO3\\T3extblog\\Domain\\Model\\Subscriber', $comment->getPostId());
		$newSubscriber->setEmail($comment->getEmail());
		$newSubscriber->setName($comment->getAuthor());

		$this->subscriberRepository->add($newSubscriber);
		$this->persistToDatabase(TRUE);

		$this->log->dev('Added subscriber uid=' . $newSubscriber->getUid());

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
		$settings = $this->settings['subscriptionManager']['subscriber'];

		if (!$settings['enableNewCommentNotifications']) {
			return;
		}

		if ($comment->getMailsSent()) {
			return;
		}

		$this->log->dev('Send subscriber notification mails.');

		/* @var $post Post */
		$post = $comment->getPost();
		$subscribers = $this->subscriberRepository->findForNotification($post);
		$subject = $this->translate('subject.subscriber.notify', $post->getTitle());

		/* @var $subscriber PostSubscriber */
		foreach ($subscribers as $subscriber) {
			// make sure we do not notify the author of the triggering comment
			if ($comment->getEmail() === $subscriber->getEmail()) {
				continue;
			}

			$subscriber->updateAuth();

			$this->subscriberRepository->update($subscriber);

			$variables = array(
				'post' => $post,
				'comment' => $comment,
				'subscriber' => $subscriber,
				'subject' => $subject,
				'validUntil' => $this->getValidUntil()
			);
			$emailBody = $this->emailService->render($variables, 'SubscriberNewCommentMail.txt');

			$this->emailService->send(
				$subscriber->getMailTo(),
				array($settings['mailFrom']['email'] => $settings['mailFrom']['name']),
				$subject,
				$emailBody
			);
		}

		$comment->setMailsSent(TRUE);
		$this->objectManager->get('TYPO3\\T3extblog\\Domain\\Repository\\CommentRepository')->update($comment);
	}

	/**
	 * Notify the blog admin
	 *
	 * @param Comment $comment
	 * @param string $emailTemplate
	 *
	 * @return    void
	 */
	protected function notifyAdmin(Comment $comment, $emailTemplate = 'AdminNewCommentMail.txt') {
		$settings = $this->settings['subscriptionManager']['admin'];

		if (!$settings['enable']) {
			return;
		}

		if (!(is_array($settings['mailTo']) && strlen($settings['mailTo']['email']) > 0)) {
			$this->log->error('No admin email configured.', $settings['mailTo']);
			return;
		}

		$this->log->dev('Send admin notification mail.');

		/* @var $post Post */
		$post = $comment->getPost();
		$subject = $this->translate('subject.admin.newSubscription', $post->getTitle());
		$variables = array(
			'post' => $post,
			'comment' => $comment,
			'subject' => $subject
		);
		$emailBody = $this->emailService->render($variables, $emailTemplate);

		$this->emailService->send(
			array($settings['mailTo']['email'] => $settings['mailTo']['name']),
			array($settings['mailFrom']['email'] => $settings['mailFrom']['name']),
			$subject,
			$emailBody
		);
	}

	/**
	 * Render dateTime object for using in template
	 *
	 * @todo We probably want to move this back to Fluid
	 *       Using a format:date VH stopped working with 7.4
	 *
	 * @return \DateTime
	 */
	protected function getValidUntil() {
		$date = new \DateTime();
		$modify = '+1 hour';

		if (isset($this->settings['subscriptionManager']['subscriber']['emailHashTimeout'])) {
			$modify = trim($this->settings['subscriptionManager']['subscriber']['emailHashTimeout']);
		}

		$date->modify($modify);

		return $date;
	}

	/**
	 * Translate helper
	 *
	 * @param string $key Translation key
	 * @param string $variable Argument for translation
	 *
	 * @return string
	 */
	protected function translate($key, $variable = '') {
		return LocalizationUtility::translate(
			$key,
			'T3extblog',
			array(
				$this->settings['blogName'],
				$variable,
			)
		);
	}

	/**
	 * Helper function for flush frontend page cache
	 *
	 * Needed as we want to make sure new comments are visible after enabling in BE.
	 *
	 * @return void
	 */
	protected function flushFrontendCache() {
		if (TYPO3_MODE === 'BE' && !empty($this->settings['blogsystem']['pid'])) {
			$this->cacheService->clearPageCache((integer)$this->settings['blogsystem']['pid']);
		}
	}

	/**
	 * Helper function for persisting all changed data to the DB
	 *
	 * Needed as in non FE controller context (aka our hook) there is no
	 * auto persisting.
	 *
	 * @param bool $force
	 *
	 * @return void
	 */
	protected function persistToDatabase($force = FALSE) {
		if ($force === TRUE || TYPO3_MODE === 'BE') {
			$this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager')->persistAll();
		}
	}
}
