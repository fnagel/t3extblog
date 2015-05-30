<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Felix Kopp <felix-source@phorax.com>
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Issue command ViewHelper, see TYPO3 Core Engine method issueCommand
 *
 * @author Felix Kopp <felix-source@phorax.com>
 * @package TYPO3
 * @subpackage t3extblog
 */
class Tx_T3extblog_ViewHelpers_LocalizationViewHelper extends Tx_Fluid_ViewHelpers_Be_AbstractBackendViewHelper {

	/**
	 * @var \TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider
	 */
	protected $translateTools;

	/**
	 * Contains sys language icons and titles
	 *
	 * @var array
	 */
	public $systemLanguages = array();

	/**
	 * @param string $table
	 * @param object $object
	 * @param string $returnUrl
	 *
	 * @return string
	 */
	public function render($table, $object, $returnUrl) {
		$content = '';
		$this->systemLanguages = $this->getTranslateTools()->getSystemLanguages($object->getPid());

		if (count($this->systemLanguages) > 2) {
			$content = $this->makeLocalizationPanel($table, $object->toArray(), $returnUrl);
		}

		return $content;
	}

	/**
	 * Creates the localization panel
	 *
	 * @param string $table The table
	 * @param array $row The record for which to make the localization panel.
	 * @return array Array with key 0/1 with content for column 1 and 2
	 * @todo Define visibility
	 */
	public function makeLocalizationPanel($table, $row, $returnUrl) {
		$out = '';
		$translations = $this->translateTools->translationInfo($table, $row['uid'], 0, $row);

		if (is_array($translations) && is_array($translations['translations'])) {
			$languageButtons = array();

			foreach ($translations['translations'] as $sysLanguageUid => $translationData) {
				if (!$GLOBALS['BE_USER']->checkLanguageAccess($sysLanguageUid)) {
					continue;
				}
				if (isset($translations['translations'][$sysLanguageUid])) {
					$languageButtons[] = $this->getLanguageIconLink(
						$sysLanguageUid,
						'alt_doc.php?edit[' . $table . '][' . $translationData['uid'] . ']=edit&returnUrl=' . $returnUrl
					);
				}
			}

			$out .= implode(' ', $languageButtons);
		}

		return $out;
	}

	protected function getLanguageIconLink($sysLanguageUid, $href) {
		$language = t3lib_BEfunc::getRecord('sys_language', $sysLanguageUid, 'title');

		if ($this->systemLanguages[$sysLanguageUid]['flagIcon']) {
			$icon = t3lib_iconWorks::getSpriteIcon($this->systemLanguages[$sysLanguageUid]['flagIcon']);
		} else {
			$icon = $this->systemLanguages[$sysLanguageUid]['title'];
		}

		return '<a href="' . htmlspecialchars($href) . '" title="' . htmlspecialchars($language['title']) . '">' . $icon . '</a>';
	}

	/**
	 * Gets an instance of TranslationConfigurationProvider
	 *
	 * @return \TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider
	 */
	protected function getTranslateTools() {
		if (!isset($this->translateTools)) {
			$this->translateTools = t3lib_div::makeInstance('TYPO3\\CMS\\Backend\\Configuration\\TranslationConfigurationProvider');
		}

		return $this->translateTools;
	}

}

?>