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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\T3extblog\Domain\Model\AbstractSubscriber;

/**
 * Handles all notification mails
 */
abstract class AbstractNotificationService implements NotificationServiceInterface, SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * subscriberRepository
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
	 * @var array
	 */
	protected $subscriptionSettings;

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
	 * Send subscriber emails
	 *
	 * @param AbstractSubscriber $subscriber
	 * @param string $subject
	 * @param string $template
	 * @param array $variables
	 *
	 * @return void
	 */
	protected function sendEmail(AbstractSubscriber $subscriber, $subject, $template, $variables = array()) {
		$settings = $this->subscriptionSettings['subscriber'];
		$defaultVariables =  array(
			'subscriber' => $subscriber,
			'subject' => $subject,
			'validUntil' => $this->getValidUntil()
		);

		$emailBody = $this->emailService->render(array_merge($defaultVariables, $variables), $template);

		$this->emailService->send(
			$subscriber->getMailTo(),
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

		if (isset($this->subscriptionSettings['subscriber']['emailHashTimeout'])) {
			$modify = trim($this->subscriptionSettings['subscriber']['emailHashTimeout']);
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
			$this->cacheService->clearPageCache((int) $this->settings['blogsystem']['pid']);
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
