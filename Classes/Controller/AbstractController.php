<?php

namespace TYPO3\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
 *  (c) 2011 Bastian Waidelich <bastian@typo3.org>
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Abstract base controller
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
abstract class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * Logging Service
	 *
	 * @var \TYPO3\T3extblog\Service\LoggingService
	 * @inject
	 */
	protected $log;

	/**
	 * Injects the Configuration Manager and is initializing the framework settings
	 * Function is used to override the merge of settings via TS & flexforms
	 * original code taken from http://forge.typo3.org/projects/typo3v4-mvc/wiki/How_to_control_override_of_TS-Flexform_configuration
	 *
	 * @param $configurationManager Tx_Extbase_Configuration_ConfigurationManagerInterface An instance of the Configuration Manager
	 *
	 * @return void
	 */
	public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;

		$tsSettings = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
			't3extblog',
			't3extblog_blogsystem'
		);

		$originalSettings = $this->configurationManager->getConfiguration(
			ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
		);

		// start override
		if (isset($tsSettings['settings']['overrideFlexformSettingsIfEmpty'])) {
			/** @var \TYPO3\T3extblog\Utility\TypoScript $typoScriptUtility */
			$typoScriptUtility = GeneralUtility::makeInstance('TYPO3\\T3extblog\\Utility\\TypoScript');
			$originalSettings = $typoScriptUtility->override($originalSettings, $tsSettings);
		}

		$this->settings = $originalSettings;
	}

	/**
	 * Override getErrorFlashMessage to present
	 * nice flash error messages.
	 *
	 * @return string
	 */
	protected function getErrorFlashMessage() {
		$defaultFlashMessage = parent::getErrorFlashMessage();
		$locallangKey = sprintf('error.%s.%s', lcfirst($this->request->getControllerName()), $this->actionMethodName);

		return $this->translate($locallangKey, $defaultFlashMessage);
	}

	/**
	 * Helper function to render localized flashmessages
	 *
	 * @param string $action
	 * @param integer $severity optional severity code. One of the FlashMessage constants
	 *
	 * @return void
	 */
	protected function addFlashMessageByKey($key, $severity = FlashMessage::OK) {
		$messageLocallangKey = sprintf('flashMessage.%s.%s', lcfirst($this->request->getControllerName()), $key);
		$localizedMessage = $this->translate($messageLocallangKey, '[' . $messageLocallangKey . ']');

		$titleLocallangKey = sprintf('%s.title', $messageLocallangKey);
		$localizedTitle = $this->translate($titleLocallangKey, '[' . $titleLocallangKey . ']');

		// @todo Remove this soon: as we do not support 4.x and 6.0 and 6.2 are no longer supported
		if (version_compare(TYPO3_branch, '6.2', '<')) {
			$this->flashMessageContainer->add($localizedMessage, $localizedTitle, $severity);
		} else {
			$this->addFlashMessage($localizedMessage, $localizedTitle, $severity);
		}
	}

	/**
	 * Helper function to check if flashmessages have been saved until now
	 *
	 * @return boolean
	 */
	protected function hasFlashMessages() {
		if (count($this->flashMessageContainer->getAllMessages()) > 0) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * helper function to use localized strings in BlogExample controllers
	 *
	 * @param string $key locallang key
	 * @param string $defaultMessage the default message to show if key was not found
	 *
	 * @return string
	 */
	protected function translate($key, $defaultMessage = '') {
		$message = LocalizationUtility::translate($key, 'T3extblog');

		if ($message === NULL) {
			$message = $defaultMessage;
		}

		return $message;
	}

	/**
	 * Clear cache of current page on error and sends correct header.
	 *
	 * @return void
	 */
	protected function clearCacheOnError() {
		parent::clearCacheOnError();

		$this->response->setHeader('Cache-Control', 'private', TRUE);
		$this->response->setHeader('Expires', '0', TRUE);
		$this->response->setHeader('Pragma', 'no-cache', TRUE);
		$this->response->sendHeaders();
	}
}
