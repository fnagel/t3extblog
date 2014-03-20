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
	 * @var boolean
	 */
	protected $renderInFe;

	/**
	 * @var Tx_T3extblog_Service_SettingsService
	 */
	protected $settingsService;

	/**
	 * @var array
	 */
	protected $settings;


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
		$this->renderInFe = $this->settings['debug']['renderInFe'];
	}


	/**
	 * Error logging
	 *
	 * @param string $msg Message
	 * @param array  $data Data
	 *
	 * @return void
	 */
	public function error($msg, $data = array()) {
		$this->writeToSysLog($msg, 3);

		if ($this->renderInFe) {
			$this->outputDebug($msg, 3, $data);
		}

		if ($this->enableDLOG || $this->logInDevlog) {
			$this->writeToDevLog($msg, 3, $data);
		}
	}

	/**
	 * Notice logging
	 *
	 * @param string $msg Message
	 * @param array  $data Data
	 *
	 * @return    void
	 */
	public function notice($msg, $data = array()) {
		$this->writeToSysLog($msg, 1);

		if ($this->renderInFe) {
			$this->outputDebug($msg, 1, $data);
		}

		if ($this->enableDLOG || $this->logInDevlog) {
			$this->writeToDevLog($msg, 1, $data);
		}
	}

	/**
	 * Development logging
	 *
	 * @param string $msg Message
	 * @param array  $data Data
	 *
	 * @return    void
	 */
	public function dev($msg, $data = array()) {
		if ($this->renderInFe) {
			$this->outputDebug($msg, 1, $data);
		}

		if ($this->enableDLOG || $this->logInDevlog) {
			$this->writeToDevLog($msg, 1, $data);
		}
	}

	/**
	 * Writes message to the FE
	 *
	 * @param string  $msg Message (in English).
	 * @param integer $severity Severity: 0 is info, 1 is notice, 2 is warning, 3 is error, 4 is fatal error
	 * @param array  $data Data
	 *
	 * @return void
	 */
	protected function outputDebug($msg, $severity = 0, $data = array()) {
		Tx_Extbase_Utility_Debugger::var_dump($data, $msg);
	}

	/**
	 * Logs message to the system log.
	 *
	 * @param string  $msg Message (in English).
	 * @param integer $severity Severity: 0 is info, 1 is notice, 2 is warning, 3 is error, 4 is fatal error
	 *
	 * @return void
	 */
	protected function writeToSysLog($msg, $severity = 0) {
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
	protected function writeToDevLog($msg, $severity = 0, $dataVar = FALSE) {
		t3lib_div::devLog($msg, $this->extKey, $severity, $dataVar);
	}

}

?>