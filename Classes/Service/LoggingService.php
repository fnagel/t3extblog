<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Felix Nagel <info@felixnagel.com>
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
 * Handles logging
 * Configured by TYPO3 core log level
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Service_LoggingService implements t3lib_Singleton {

	/**
	 * The extension key
	 *
	 * @var boolean
	 */
	protected $extKey = 't3extblog';

	/**
	 * @var boolean
	 */
	protected $enableDLOG;

	/**
	 * @var boolean
	 */
	protected $logInDevlog;

	/**
	 * @var Tx_T3extblog_Service_SettingsService
	 */
	protected $settingsService;


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
		$this->enableDLOG = $GLOBALS['TYPO3_CONF_VARS']['SYS']['enable_DLOG'];

		$this->logInDevlog = $this->settings['debug']['logInDevlog'];
	}


	/**
	 * Error logging
	 *
	 * @param    string    Message
	 * @param    array    Data
	 *
	 * @return    void
	 */
	public function error($msg, $data) {
		$this->sysLog($msg, 3);

		if ($this->enableDLOG || $this->logInDevlog) {
			$this->devLog($msg, 3, $data);
		}
	}

	/**
	 * Notice logging
	 *
	 * @param    string    Message
	 * @param    array    Data
	 *
	 * @return    void
	 */
	public function notice($msg, $data) {
		$this->sysLog($msg, 1);

		if ($this->enableDLOG || $this->logInDevlog) {
			$this->devLog($msg, 1, $data);
		}
	}

	/**
	 * Development logging
	 *
	 * @param    string    Message
	 * @param    array    Data
	 *
	 * @return    void
	 */
	public function dev($msg, $data) {
		if ($this->enableDLOG || $this->logInDevlog) {
			$this->devLog($msg, 1, $data);
		}
	}

	/**
	 * Logs message to the system log.
	 *
	 * @param string  $msg Message (in English).
	 * @param integer $severity Severity: 0 is info, 1 is notice, 2 is warning, 3 is error, 4 is fatal error
	 *
	 * @return void
	 */
	protected function sysLog($msg, $severity = 0) {
		t3lib_div::sysLog($msg, $this->extKey, $severity);
	}

	/**
	 * Logs message to the development log.
	 *
	 * @param string  $msg Message (in english).
	 * @param integer $severity Severity: 0 is info, 1 is notice, 2 is warning, 3 is fatal error, -1 is "OK" message
	 * @param mixed   $dataVar Additional data you want to pass to the logger.
	 *
	 * @return void
	 */
	protected function devLog($msg, $severity = 0, $dataVar = FALSE) {
		t3lib_div::devLog($msg, $this->extKey, $severity, $dataVar);
	}

}

?>