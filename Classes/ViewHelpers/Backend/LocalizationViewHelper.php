<?php

namespace TYPO3\T3extblog\ViewHelpers\Backend;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;

/**
 * Issue command ViewHelper, see TYPO3 Core Engine method issueCommand
 *
 * @author Felix Kopp <felix-source@phorax.com>
 * @package TYPO3
 * @subpackage t3extblog
 */
class LocalizationViewHelper extends AbstractBackendViewHelper {

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
	 * @param string $translations Name of the added variable
	 * @param string $table Table to process
	 * @param object $object Object to process
	 * @param string $returnUrl BE return url
	 *
	 * @return string
	 */
	public function render($translations, $table, $object, $returnUrl) {
		$content = '';
		$templateVariableContainer = $this->renderingContext->getTemplateVariableContainer();

		$this->systemLanguages = $this->getTranslateTools()->getSystemLanguages($object->getPid());
		if (count($this->systemLanguages) > 2) {
			$records = $this->getLocalizedRecords($table, $object->toArray(), $returnUrl);

			$templateVariableContainer->add($translations, $records);
			$content = $this->renderChildren();
			$templateVariableContainer->remove($translations);
		}

		return $content;
	}

	/**
	 * Creates the localization panel
	 *
	 * @param string $table The table
	 * @param array $row The record for which to make the localization panel.
	 * @param string $returnUrl
	 *
	 * @return array
	 */
	public function getLocalizedRecords($table, $row, $returnUrl) {
		$records = array();
		$translations = $this->translateTools->translationInfo($table, $row['uid'], 0, $row);

		if (is_array($translations) && is_array($translations['translations'])) {

			foreach ($translations['translations'] as $sysLanguageUid => $translationData) {
				if (!$GLOBALS['BE_USER']->checkLanguageAccess($sysLanguageUid)) {
					continue;
				}

				if (isset($translations['translations'][$sysLanguageUid])) {
					$records[$sysLanguageUid] = array(
						'editIcon' => $this->getLanguageIconLink(
							$sysLanguageUid,
							'alt_doc.php?edit[' . $table . '][' . $translationData['uid'] . ']=edit&returnUrl=' . $returnUrl,
							$translationData['uid']
						),
						'uid' => $translations['translations'][$sysLanguageUid]['uid']
					);
				}
			}
		}

		return $records;
	}

	protected function getLanguageIconLink($sysLanguageUid, $href, $uid) {
		$language = BackendUtility::getRecord('sys_language', $sysLanguageUid, 'title');

		if ($this->systemLanguages[$sysLanguageUid]['flagIcon']) {
			$icon = IconUtility::getSpriteIcon($this->systemLanguages[$sysLanguageUid]['flagIcon']);
		} else {
			$icon = $this->systemLanguages[$sysLanguageUid]['title'];
		}

		return '<a href="' . htmlspecialchars($href) . '" title="' . $uid . ', ' . htmlspecialchars($language['title']) . '">' . $icon . '</a>';
	}

	/**
	 * Gets an instance of TranslationConfigurationProvider
	 *
	 * @return \TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider
	 */
	protected function getTranslateTools() {
		if (!isset($this->translateTools)) {
			$this->translateTools = GeneralUtility::makeInstance(
				'TYPO3\\CMS\\Backend\\Configuration\\TranslationConfigurationProvider'
			);
		}

		return $this->translateTools;
	}

}
