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
 * Handles email sending and templating
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Service_EmailService implements t3lib_Singleton {

	/**
	 * Extension name
	 *
	 * @var string
	 */
	protected $extensionName = 't3extblog';

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

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
	 *
	 */
	public function initializeObject() {
		$this->settings = $this->settingsService->getTypoScriptSettings();
	}

	/**
	 * This is the main-function for sending Mails
	 *
	 * @param array  $mailTo
	 * @param array  $mailFrom
	 * @param string $subject
	 * @param string $emailBody
	 *
	 * @return integer the number of recipients who were accepted for delivery
	 */
	public function send($mailTo, $mailFrom, $subject, $emailBody) {
		if (!($mailTo && is_array($mailTo) && t3lib_div::validEmail(key($mailTo)))) {
			$this->log->error("Given mailto email address is invalid.", $mailTo);
			return FALSE;
		}

		if (!($mailFrom && is_array($mailFrom) && t3lib_div::validEmail(key($mailFrom)))) {
			$mailFrom = t3lib_utility_Mail::getSystemFrom();
		}

		/* @var $message t3lib_mail_Message */
		$message = $this->objectManager->create('t3lib_mail_Message');
		$message
			->setTo($mailTo)
			->setFrom($mailFrom)
			->setSubject($subject)
			->setCharset($GLOBALS['TSFE']->metaCharset);

		// send text or html emails
		if (strip_tags($emailBody) === $emailBody) {
			$message->setBody($emailBody, 'text/plain');
		} else {
			$message->setBody($emailBody, 'text/html');
		}

		if (!$this->settings["debug"]["disableEmailTransmission"]) {
			$message->send();
		}

		$logData = array(
			'mailTo' => $mailTo,
			'mailFrom' => $mailFrom,
			'subject' => $subject,
			'emailBody' => $emailBody,
			'isSent' => $message->isSent()
		);
		$this->log->dev("Email sent.", $logData);

		return $logData['isSent'];
	}

	/**
	 * This functions renders template to use in Mails and Other views
	 *
	 * @param array  $variables Arguments for template
	 * @param string $templatePath Choose a template
	 * @param string $format Choose a format (txt or html)
	 */
	public function render($variables, $templatePath = "Default.txt", $format = 'txt') {
		$frameworkConfig = $this->settingsService->getFrameworkSettings();
		$emailView = $this->objectManager->create('Tx_Fluid_View_StandaloneView');

		$emailView->setFormat($format);

		$emailView->setLayoutRootPath(t3lib_div::getFileAbsFileName($frameworkConfig['email']['layoutRootPath']));
		$emailView->setPartialRootPath(t3lib_div::getFileAbsFileName($frameworkConfig['email']['partialRootPath']));
		$emailView->setTemplatePathAndFilename(
			t3lib_div::getFileAbsFileName($frameworkConfig['email']['templateRootPath']) . $templatePath
		);

		$emailView->getRequest()->setPluginName('');
		$emailView->getRequest()->setControllerName('');
		$emailView->getRequest()->setControllerExtensionName($this->extensionName);

		$emailView->assignMultiple($variables);
		$emailView->assignMultiple(array(
			'timestamp' => $GLOBALS['EXEC_TIME'],
			'domain' => t3lib_div::getIndpEnv('TYPO3_SITE_URL'),
			'settings' => $this->settings
		));

		return $emailView->render();
	}
}

?>