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
use TYPO3\T3extblog\Domain\Model\BlogSubscriber;

/**
 * Handles all notification mails for new posts notification
 */
class BlogNotificationService extends AbstractNotificationService {

	/**
	 * subscriberRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\BlogSubscriberRepository
	 * @inject
	 */
	protected $subscriberRepository;

	/**
	 * @return void
	 */
	public function initializeObject() {
		parent::initializeObject();

		$this->subscriptionSettings = $this->settingsService->getTypoScriptByPath('subscriptionManager.blog');
	}

	/**
	 * Process added subscriber
	 *
	 * @param BlogSubscriber $subscriber
	 *
	 * @return void
	 */
	public function processNewEntity($subscriber) {
		if (!($subscriber instanceof BlogSubscriber)) {
			throw new \InvalidArgumentException('Object should be of type BlogSubscriber!');
		}

		if ($subscriber->isValidForOptin()) {
			$this->sendOptInMail($subscriber);
			$this->persistToDatabase();
		}
	}

	/**
	 * Process changed status of a subscriber
	 *
	 * @param BlogSubscriber $subscriber
	 *
	 * @return void
	 */
	public function processChangedStatus($subscriber) {
		if (!($subscriber instanceof BlogSubscriber)) {
			throw new \InvalidArgumentException('Object should be of type BlogSubscriber!');
		}

		if ($subscriber->isValidForOptin()) {
			$this->sendOptInMail($subscriber);
			$this->persistToDatabase();
		}
	}

	/**
	 * Send optin mail for subscriber
	 *
	 * @param BlogSubscriber $subscriber
	 *
	 * @return void
	 */
	protected function sendOptInMail(BlogSubscriber $subscriber) {
		$this->log->dev('Send blog subscriber opt-in mail.');

		$subscriber->updateAuth();
		$this->subscriberRepository->update($subscriber);

		$this->sendEmail(
			$subscriber,
			$this->translate('subject.subscriber.new'),
			$this->subscriptionSettings['subscriber']['template']['confirm']
		);
	}

	/**
	 * Send post notification mails
	 *
	 * @param Post $post
	 *
	 * @return int Amount of subscribers
	 */
	public function notifySubscribers(Post $post) {
		$settings = $this->subscriptionSettings['subscriber'];

		if (!$settings['enableNotifications']) {
			return;
		}

		if ($post->getMailsSent()) {
			return;
		}

		$subscribers = $this->subscriberRepository->findForNotification();
		$subject = $this->translate('subject.subscriber.notify', $post->getTitle());

		$this->log->dev('Send blog subscriber notification mails to ' . count($subscribers) . ' users.');

		/* @var $subscriber BlogSubscriber */
		foreach ($subscribers as $subscriber) {
			$subscriber->updateAuth();
			$this->subscriberRepository->update($subscriber);

			$this->sendEmail($subscriber, $subject, $settings['template']['notification'], array('post' => $post));
		}

		$post->setMailsSent(TRUE);
		$this->objectManager->get('TYPO3\\T3extblog\\Domain\\Repository\\PostRepository')->update($post);
		$this->persistToDatabase();

		return count($subscribers);
	}

}
