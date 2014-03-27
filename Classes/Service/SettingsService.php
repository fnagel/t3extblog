<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Sebastian Schreiber <me@schreibersebastian.de >
 *  (c) 2010 Georg Ringer <typo3@ringerge.org>
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
 * Provide a way to get the configuration just everywhere
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Tx_T3extblog_Service_SettingsService implements t3lib_Singleton {

	/**
	 * Extension name
	 *
	 * Needed as parameter for configurationManager->getConfiguration when used in BE context
	 * Otherwise generated TS will be incorrect or missing
	 *
	 * @var string
	 */
	protected $extensionName = 't3extblog';

	/**
	 * Plugin name
	 *
	 * Needed as parameter for configurationManager->getConfiguration when used in BE context
	 * Otherwise generated TS will be incorrect or missing when used in BE
	 *
	 * @var string
	 */
	protected $pluginName = '';

	/**
	 * @var mixed
	 */
	protected $typoScriptSettings = NULL;

	/**
	 * @var mixed
	 */
	protected $frameworkSettings = NULL;

	/**
	 * @var Tx_Extbase_Configuration_ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * Injects the Configuration Manager and loads the settings
	 *
	 * @param Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager An instance of the Configuration Manager
	 *
	 * @return void
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Returns all framework settings.
	 *
	 * @return array
	 */
	public function getFrameworkSettings() {
		if ($this->frameworkSettings === NULL) {
			$this->frameworkSettings = $this->configurationManager->getConfiguration(
				Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
				$this->extensionName,
				$this->pluginName
			);
		}

		if ($this->frameworkSettings === NULL) {
			throw new Tx_Extbase_Configuration_Exception('No framework typoscript settings available.');
		}

		return $this->frameworkSettings;
	}

	/**
	 * Returns all TS settings.
	 *
	 * @return array
	 */
	public function getTypoScriptSettings() {
		if ($this->typoScriptSettings === NULL) {
			$this->typoScriptSettings = $this->configurationManager->getConfiguration(
				Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
				$this->extensionName,
				$this->pluginName
			);
		}

		if ($this->typoScriptSettings === NULL) {
			throw new Tx_Extbase_Configuration_Exception('No typoscript settings available.');
		}

		return $this->typoScriptSettings;
	}

	/**
	 * Returns the settings at path $path, which is separated by ".",
	 * e.g. "pages.uid".
	 * "pages.uid" would return $this->settings['pages']['uid'].
	 *
	 * If the path is invalid or no entry is found, false is returned.
	 *
	 * @param string $path
	 *
	 * @return mixed
	 */
	public function getTypoScriptByPath($path) {
		return Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($this->getTypoScriptSettings(), $path);
	}

	/**
	 * Helper method which forces the generation of the TypoScript for a specific page
	 *
	 * @param int $pageId
	 * @return array The TypoScript setup
	 */
	public function forceLoadTypoScript($pageId = 0) {
		$template = t3lib_div::makeInstance('t3lib_TStemplate');
		// do not log time-performance information
		$template->tt_track = 0;
		$template->init();
		// Get the root line
		$sysPage = t3lib_div::makeInstance('t3lib_pageSelect');
		// get the rootline for the current page
		$rootline = $sysPage->getRootLine($pageId);
		// This generates the constants/config + hierarchy info for the template.
		$template->runThroughTemplates($rootline, 0);
		$template->generateConfig();

		/* @var $typoScriptService Tx_Extbase_Service_TypoScriptService */
		$typoScriptService = t3lib_div::makeInstance('Tx_Extbase_Service_TypoScriptService');
		$typoScript = $typoScriptService->convertTypoScriptArrayToPlainArray($template->setup);

		return $typoScript;
	}

	/**
	 * Set page uid in GP vars
	 *
	 * Only needed when the class is called or injected in a BE context, e.g. a hook
	 * Needed for generation of the correct persistence.storagePid in Extbase TS.
	 * Without the generation of the TS is based upon the next root page (default
	 * extbase behaviour) and repositories won't work as expected.
	 *
	 * @param $pageUid
	 */
	public function setPageUid($pageUid) {
		if (TYPO3_MODE === 'BE') {
			t3lib_div::_GETset(intval($pageUid), 'id');
		}
	}
}

?>