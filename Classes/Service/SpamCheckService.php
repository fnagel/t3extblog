<?php

namespace TYPO3\T3extblog\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2015 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\T3extblog\Domain\Model\Comment;
use TYPO3\CMS\Extbase\Mvc\Request;

/**
 * Handles comment spam check
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class SpamCheckService implements SpamCheckServiceInterface {

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
	protected $spamSettings;

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
	 * @param Comment $comment The comment to be checked
	 * @param Request $request The request to be checked
	 *
	 * @return integer
	 */
	public function process(Comment $comment, Request $request) {
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
			if (GeneralUtility::getIndpEnv('HTTP_USER_AGENT') == '') {
				$spamPoints += intval($this->spamSettings['userAgent']);
			}
		}

		$comment->setSpamPoints($spamPoints);

		return $spamPoints;
	}

	/**
	 * Checks honeypot fields
	 *
	 * @param Request $request The request to be checked
	 *
	 * @return boolean
	 */
	protected function checkHoneyPotFields(Request $request) {
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
