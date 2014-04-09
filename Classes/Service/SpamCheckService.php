<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Felix Nagel <info@felixnagel.com>
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
 * Handles comment spam check
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Service_SpamCheckService implements Tx_T3extblog_Service_SpamCheckServiceInterface {

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
	 * @var array
	 */
	protected $spamSettings;

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
	 * @return void
	 */
	public function initializeObject() {
		$this->settings = $this->settingsService->getTypoScriptSettings();
		$this->spamSettings = $this->settings['blogsystem']['comments']['spamCheck'];
	}

	/**
	 * Checks comment for SPAM
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment The comment to be checked
	 * @param Tx_Extbase_MVC_Request $request The request to be checked
	 *
	 * @return integer
	 */
	public function process(Tx_T3extblog_Domain_Model_Comment $comment, Tx_Extbase_MVC_Request $request) {
		$spamPoints = 0;

		if (!$this->spamSettings['enable']) {
			return $spamPoints;
		}

		if ($this->spamSettings['honeypot']) {
			if (!$this->checkHoneyPotFields($request)) {
				$spamPoints += intval($this->spamSettings['honeypot']);
			}
		}

		if ($this->spamSettings['isHumanCheckbox']) {
			if (!$request->hasArgument('human') || !$request->hasArgument('human')) {
				$spamPoints += intval($this->spamSettings['isHumanCheckbox']);
			}
		}

		if ($this->spamSettings['cookie']) {
			if (!$_COOKIE['fe_typo_user']) {
				$spamPoints += intval($this->spamSettings['cookie']);
			}
		}

		if ($this->spamSettings['userAgent']) {
			if (t3lib_div::getIndpEnv('HTTP_USER_AGENT') == '') {
				$spamPoints += intval($this->spamSettings['userAgent']);
			}
		}

		if ($this->spamSettings['sfpantispam']) {
			if ($this->checkCommentWithSfpAntiSpam($comment)) {
				$spamPoints += intval($this->spamSettings['sfpantispam']);
			}
		}

		$comment->setSpamPoints($spamPoints);

		return $spamPoints;
	}

	/**
	 * Checks text fields with EXT:sfpantispam
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 *
	 * @return boolean
	 */
	protected function checkCommentWithSfpAntiSpam(Tx_T3extblog_Domain_Model_Comment $comment) {
		if (!t3lib_extMgm::isLoaded('sfpantispam')) {
			$this->log->error('EXT:sfpantispam not installed but enabled in configuration.');
			return FALSE;
		}

		/* @var $sfpAntiSpam tx_sfpantispam_tslibfepreproc */
		$sfpAntiSpam = t3lib_div::makeInstance('tx_sfpantispam_tslibfepreproc');
		$fields = array(
			$comment->getAuthor(),
			$comment->getTitle(),
			$comment->getWebsite(),
			$comment->getEmail(),
			$comment->getText()
		);

		return !$sfpAntiSpam->sendFormmail_preProcessVariables($fields, $this);
	}

	/**
	 * Checks honeypot fields
	 *
	 * @param Tx_Extbase_MVC_Request $request The request to be checked
	 *
	 * @return boolean
	 */
	protected function checkHoneyPotFields(Tx_Extbase_MVC_Request $request) {
		if (!$request->hasArgument('author') || strlen($request->getArgument('author')) > 0) {
			return FALSE;
		}
		if (!$request->hasArgument('link') || strlen($request->getArgument('link')) > 0) {
			return FALSE;
		}
		if (!$request->hasArgument('text') || strlen($request->getArgument('text')) > 0) {
			return FALSE;
		}
		if (!$request->hasArgument('timestamp') || $request->getArgument('timestamp') !== '1368283172') {
			return FALSE;
		}

		return TRUE;
	}
}

?>