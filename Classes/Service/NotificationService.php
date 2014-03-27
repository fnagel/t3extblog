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
 * Handles all notification mails
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Service_NotificationService implements Tx_T3extblog_Service_NotificationServiceInterface, t3lib_Singleton {

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * subscriberRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_SubscriberRepository
	 */
	protected $subscriberRepository;

	/**
	 * Logging Service
	 *
	 * @var Tx_T3extblog_Service_LoggingService
	 */
	protected $log;

	/**
	 * @var Tx_T3extblog_Service_SettingsService
	 */
	protected $settingsService;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var Tx_T3extblog_Service_EmailService $emailService
	 */
	protected $emailService;

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 *
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Injects the Logging Service
	 *
	 * @param Tx_T3extblog_Service_LoggingService $loggingService
	 *
	 * @return void
	 */
	public function injectLoggingService(Tx_T3extblog_Service_LoggingService $loggingService) {
		$this->log = $loggingService;
	}

	/**
	 * Injects the Subscriber Repository
	 *
	 * @param Tx_T3extblog_Domain_Repository_SubscriberRepository $subscriberRepository
	 *
	 * @return void
	 */
	public function injectSubscriberRepository(Tx_T3extblog_Domain_Repository_SubscriberRepository $subscriberRepository) {
		$this->subscriberRepository = $subscriberRepository;
	}

	/**
	 * Injects the Settings Service
	 *
	 * @param Tx_T3extblog_Service_SettingsService $settingsService
	 *
	 * @return void
	 */
	public function injectSettingsService(Tx_T3extblog_Service_SettingsService $settingsService) {
		$this->settingsService = $settingsService;
	}

	/**
	 * @param Tx_T3extblog_Service_EmailService $emailService
	 */
	public function injectEmailService(Tx_T3extblog_Service_EmailService $emailService) {
		$this->emailService = $emailService;
	}

	/**
	 *
	 */
	public function initializeObject() {
		$this->settings = $this->settingsService->getTypoScriptSettings();
	}

	/**
	 * Process added comment
	 * Comment is already persisted to DB
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment Comment
	 * @param boolean $notifyAdmin
	 *
	 * @return void
	 */
	public function processCommentAdded(Tx_T3extblog_Domain_Model_Comment $comment, $notifyAdmin = TRUE) {
		if ($notifyAdmin === TRUE) {
			$this->notifyAdmin($comment);
		}

		if ($this->isNewSubscriptionValid($comment)) {
			$subscriber = $this->addSubscriber($comment);
		}

		if ($comment->isValid()) {
			if ($subscriber instanceof Tx_T3extblog_Domain_Model_Subscriber) {
				$this->sendOptInMail($subscriber, $comment);
			}

			$this->notifySubscribers($comment);

			$this->persistToDatabase();
		}

	}

	/**
	 * Process changed status of a comment
	 * Comment is already persisted to DB
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment Comment
	 *
	 * @return void
	 */
	public function processCommentStatusChanged(Tx_T3extblog_Domain_Model_Comment $comment) {
		if ($comment->isValid()) {
			$subscriber = $this->subscriberRepository->findForSubscriptionMail($comment);
			if ($subscriber instanceof Tx_T3extblog_Domain_Model_Subscriber) {
				$this->sendOptInMail($subscriber, $comment);
			}

			$this->notifySubscribers($comment);

			$this->persistToDatabase();
		}
	}

	/**
	 * Checks if a new subscription should be added
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 *
	 * @return boolean
	 */
	protected function isNewSubscriptionValid(Tx_T3extblog_Domain_Model_Comment $comment) {
		if (!$this->settings['blogsystem']['comments']['subscribeForComments'] || !$comment->getSubscribe()) {
			return FALSE;
		}

		// check if user already registered
		$subscribers = $this->subscriberRepository->findExistingSubscriptions($comment->getPostId(), $comment->getEmail());
		if (count($subscribers) > 0) {
			$this->log->notice("Subscriber [" . $comment->getEmail() . "] already registered.");
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Send optin mail for subscirber
	 *
	 * @param Tx_T3extblog_Domain_Model_Subscriber $subscriber
	 * @param Tx_T3extblog_Domain_Model_Comment    $comment Comment
	 *
	 * @return void
	 */
	protected function sendOptInMail(Tx_T3extblog_Domain_Model_Subscriber $subscriber, Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->log->dev("Send subscriber opt-in mail.");

		$post = $subscriber->getPost();
		$subscriber->updateAuth();

		$subject = $this->translate('subject.subscriber.new', $post->getTitle());
		$variables = array(
			'post' => $post,
			'comment' => $comment,
			'subscriber' => $subscriber,
			'subject' => $subject
		);
		$emailBody = $this->emailService->render($variables, "SubscriberOptinMail.txt");

		$this->emailService->send(
			$subscriber->getMailTo(),
			$this->settings['subscriptionManager']['subscriber']['mailFrom'],
			$subject,
			$emailBody
		);
	}

	/**
	 * Send
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 *
	 * @return Tx_T3extblog_Domain_Model_Subscriber
	 */
	protected function addSubscriber(Tx_T3extblog_Domain_Model_Comment $comment) {
		/* @var $newSubscriber Tx_T3extblog_Domain_Model_Subscriber */
		$newSubscriber = t3lib_div::makeInstance('Tx_T3extblog_Domain_Model_Subscriber', $comment->getPostId());
		$newSubscriber->setEmail($comment->getEmail());
		$newSubscriber->setName($comment->getAuthor());

		$this->subscriberRepository->add($newSubscriber);
		$this->persistToDatabase(TRUE);

		$this->log->dev("Added subscriber uid=" . $newSubscriber->getUid());

		return $newSubscriber;
	}

	/**
	 * Send comment notification mails
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 *
	 * @return    void
	 */
	protected function notifySubscribers(Tx_T3extblog_Domain_Model_Comment $comment) {
		$settings = $this->settings['subscriptionManager']['subscriber'];

		if (!$settings['enableNewCommentNotifications']) {
			return;
		}

		if ($comment->getMailsSent()) {
			return;
		}

		$this->log->dev("Send subscriber notification mails.");

		/* @var $post Tx_T3extblog_Domain_Model_Post */
		$post = $comment->getPost();
		$subscribers = $this->subscriberRepository->findForNotification($post);
		$subject = $this->translate('subject.subscriber.notify', $post->getTitle());

		/* @var $subscriber Tx_T3extblog_Domain_Model_Subscriber */
		foreach ($subscribers as $subscriber) {
			// make sure we do not notify the author of the triggering comment
			if ($comment->getEmail() === $subscriber->getEmail()) {
				continue;
			}

			$subscriber->updateAuth();

			$variables = array(
				'post' => $post,
				'comment' => $comment,
				'subscriber' => $subscriber,
				'subject' => $subject
			);
			$emailBody = $this->emailService->render($variables, 'SubscriberNewCommentMail.txt');

			$this->emailService->send($subscriber->getMailTo(), $settings['mailFrom'], $subject, $emailBody);
		}

		$comment->setMailsSent(TRUE);
	}

	/**
	 * Notify the blog admin
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @param string                            $emailTemplate
	 *
	 * @return    void
	 */
	protected function notifyAdmin(Tx_T3extblog_Domain_Model_Comment $comment, $emailTemplate = "AdminNewCommentMail.txt") {
		$settings = $this->settings['subscriptionManager']['admin'];

		if (!$settings['enable']) {
			return;
		}

		if (!(is_array($settings['mailTo']) && strlen($settings['mailTo']['email']) > 0)) {
			$this->log->error('No admin email configured.', $settings['mailTo']);
			return;
		}

		$this->log->dev('Send admin notification mail.');

		/* @var $post Tx_T3extblog_Domain_Model_Post */
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
	 * Translate helper
	 *
	 * @param string $key Translation key
	 * @param string $variable Argument for translation
	 *
	 * @return string
	 */
	protected function translate($key, $variable = "") {
		return Tx_Extbase_Utility_Localization::translate(
			$key,
			'T3extblog',
			array(
				$this->settings['blogName'],
				$variable,
			)
		);
	}

	/**
	 * Helper function for persisting all changed data to the DB
	 *
	 * Needed as in non FE controller context (aka our hook) there is no
	 * auto persisting.
	 *
	 * @param bool $force
	 */
	protected function persistToDatabase($force = FALSE) {
		if ($force === TRUE || TYPO3_MODE === 'BE') {
			$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();
		}
	}
}

?>