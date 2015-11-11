<?php

namespace TYPO3\T3extblog\ViewHelpers\Backend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;

/**
 * Show localized posts view helper
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
	 *
	 * @return string
	 */
	public function render($translations, $table, $object) {
		$content = '';
		$templateVariableContainer = $this->renderingContext->getTemplateVariableContainer();

		$this->systemLanguages = $this->getTranslateTools()->getSystemLanguages($object->getPid());
		if (count($this->systemLanguages) > 2) {
			$records = $this->getLocalizedRecords($table, $object->toArray());

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
	 *
	 * @return array
	 */
	public function getLocalizedRecords($table, $row) {
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
							BackendUtility::editOnClick('&edit[' . $table . '][' . $translationData['uid'] . ']=edit'),
							$translationData['uid']
						),
						'uid' => $translations['translations'][$sysLanguageUid]['uid']
					);
				}
			}
		}

		return $records;
	}

	protected function getLanguageIconLink($sysLanguageUid, $onclick, $uid) {
		$language = BackendUtility::getRecord('sys_language', $sysLanguageUid, 'title');

		if ($this->systemLanguages[$sysLanguageUid]['flagIcon']) {
			// @todo Remove this when 6.2 is no longer relevant
			if (version_compare(TYPO3_branch, '7.0', '<')) {
				$icon = IconUtility::getSpriteIcon($this->systemLanguages[$sysLanguageUid]['flagIcon']);
			} else {
				/* @var $iconFactory \TYPO3\CMS\Core\Imaging\IconFactory */
				$iconFactory = GeneralUtility::makeInstance(IconFactory::class);
				$icon = $iconFactory->getIcon($this->systemLanguages[$sysLanguageUid]['flagIcon'], Icon::SIZE_SMALL)->render();
			}
		} else {
			$icon = $this->systemLanguages[$sysLanguageUid]['title'];
		}

		return '<a href="" onclick="' . htmlspecialchars($onclick) . '" title="' . $uid . ', ' .
			htmlspecialchars($language['title']) . '">' . $icon . '</a>';
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
