<?php

namespace TYPO3\T3extblog\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2016 Felix Nagel <info@felixnagel.com>
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Handles email sending and templating
 */
class EmailService implements SingletonInterface {

	/**
	 * Extension name
	 *
	 * @var string
	 */
	protected $extensionName = 't3extblog';

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

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
	 * @return void
	 */
	public function initializeObject() {
		$this->settings = $this->settingsService->getTypoScriptSettings();
	}

	/**
	 * This is the main-function for sending Mails
	 *
	 * @param array $mailTo
	 * @param array $mailFrom
	 * @param string $subject
	 * @param string $emailBody
	 *
	 * @return integer the number of recipients who were accepted for delivery
	 */
	public function send($mailTo, $mailFrom, $subject, $emailBody) {
		if (!($mailTo && is_array($mailTo) && GeneralUtility::validEmail(key($mailTo)))) {
			$this->log->error('Given mailto email address is invalid.', $mailTo);

			return FALSE;
		}

		if (!($mailFrom && is_array($mailFrom) && GeneralUtility::validEmail(key($mailFrom)))) {
			$mailFrom = MailUtility::getSystemFrom();
		}

		/* @var $message \TYPO3\CMS\Core\Mail\MailMessage */
		$message = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
		$message
			->setTo($mailTo)
			->setFrom($mailFrom)
			->setSubject($subject)
			->setCharset(\TYPO3\T3extblog\Utility\GeneralUtility::getTsFe()->metaCharset);

		// Plain text only
		if (strip_tags($emailBody) == $emailBody) {
			$message->setBody($emailBody, 'text/plain');
		} else {
			// Send as HTML and plain text
			$message->setBody($emailBody, 'text/html');
			$message->addPart($this->preparePlainTextBody($emailBody), 'text/plain' );
		}

		if (!$this->settings['debug']['disableEmailTransmission']) {
			$message->send();
		}

		$logData = array(
			'mailTo' => $mailTo,
			'mailFrom' => $mailFrom,
			'subject' => $subject,
			'emailBody' => $emailBody,
			'isSent' => $message->isSent()
		);
		$this->log->dev('Email sent.', $logData);

		return $logData['isSent'];
	}

	/**
	 * This functions renders template to use in Mails and Other views
	 *
	 * @param array $variables Arguments for template
	 * @param string $templatePath Choose a template
	 *
	 * @return string
	 */
	public function render($variables, $templatePath = 'Default.txt') {
		/* @var $emailView \TYPO3\CMS\Fluid\View\StandaloneView */
		$emailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

		$this->setPathsAndFile($emailView, $templatePath);

		$format = array_pop(explode('.', $templatePath));
		$emailView->setFormat($format);

		$emailView->getRequest()->setPluginName('');
		$emailView->getRequest()->setControllerName('');
		$emailView->getRequest()->setControllerExtensionName($this->extensionName);

		$emailView->assignMultiple($variables);
		$emailView->assignMultiple(array(
			'timestamp' => $GLOBALS['EXEC_TIME'],
			'domain' => GeneralUtility::getIndpEnv('TYPO3_SITE_URL'),
			'settings' => $this->settings
		));

		return $emailView->render();
	}

	/**
	 * Set paths and file to standalone view
	 *
	 * @param \TYPO3\CMS\Fluid\View\StandaloneView $emailView
	 * @param string $templatePath Choose a template
	 *
	 * @return void
	 */
	public function setPathsAndFile(StandaloneView $emailView, $templatePath) {
		$frameworkConfig = $this->settingsService->getFrameworkSettings();

		// @todo Remove this when TYPO3 6.2 is no longer relevant
		if (version_compare(TYPO3_branch, '6.2', '<=')) {
			$emailView->setLayoutRootPath(GeneralUtility::getFileAbsFileName($frameworkConfig['email']['layoutRootPath']));
			$emailView->setPartialRootPath(GeneralUtility::getFileAbsFileName($frameworkConfig['email']['partialRootPath']));
			$emailView->setTemplatePathAndFilename(
				GeneralUtility::getFileAbsFileName($frameworkConfig['email']['templateRootPath']) . $templatePath
			);

			return;
		}

		// TYPO3 7.x with fallback for old settings
		// @todo Remove else statements when TYPO3 6.2 is no longer relevant
		if (isset($frameworkConfig['email']['layoutRootPaths'])) {
			$layoutPaths = $frameworkConfig['email']['layoutRootPaths'];
		} else {
			$layoutPaths = array(GeneralUtility::getFileAbsFileName($frameworkConfig['email']['layoutRootPath']));
		}

		if (isset($frameworkConfig['email']['partialRootPaths'])) {
			$partialPaths = $frameworkConfig['email']['partialRootPaths'];
		} else {
			$partialPaths = array(GeneralUtility::getFileAbsFileName($frameworkConfig['email']['partialRootPath']));
		}

		if (isset($frameworkConfig['email']['templateRootPaths'])) {
			$rootPaths = $frameworkConfig['email']['templateRootPaths'];
		} else {
			$rootPaths = array(GeneralUtility::getFileAbsFileName($frameworkConfig['email']['templateRootPath']));
		}

		$emailView->setLayoutRootPaths($layoutPaths);
		$emailView->setPartialRootPaths($partialPaths);
		$emailView->setTemplateRootPaths($rootPaths);
		$emailView->setTemplate($templatePath);
	}

	/**
	 * Prepare html as plain text
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	protected function preparePlainTextBody($html) {
		$output = preg_replace('/<style\\b[^>]*>(.*?)<\\/style>/s', '', $html);
		$output = strip_tags(preg_replace('/<a.* href=(?:"|\')(.*)(?:"|\').*>/', '$1', $output));
		$output = GeneralUtility::substUrlsInPlainText($output);
		$output = MailUtility::breakLinesForEmail($output);
		$output = preg_replace('/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n\n", $output);

		return $output;
	}
}
