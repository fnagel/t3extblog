<?php

namespace TYPO3\T3extblog\ViewHelpers\Frontend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2016 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Fluid\ViewHelpers\FlashMessagesViewHelper as BaseFlashMessagesViewHelper;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * View helper which renders the flash messages
 *
 * Extended to fix a caching issue
 */
class FlashMessagesViewHelper extends BaseFlashMessagesViewHelper {

	/**
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObject;

	/**
	 * Taken from TYPO3 6.2 to restore cache behaviour
	 *
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->contentObject = $configurationManager->getContentObject();
	}

	/**
	 * @todo Remove this when dropping TYPO3 7.6 support
	 *
	 * @inheritdoc
	 */

	/**
	 * Renders FlashMessages and flushes the FlashMessage queue
	 * Note: This disables the current page cache in order to prevent FlashMessage output
	 * from being cached.
	 *
	 * @see \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::no_cache
	 * @param string $renderMode @deprecated since TYPO3 CMS 7.3. If you need custom output, use <f:flashMessages as="messages"><f:for each="messages" as="message">...</f:for></f:flashMessages>
	 * @param string $as The name of the current flashMessage variable for rendering inside
	 * @return string rendered Flash Messages, if there are any.
	 * @api
	 */
	public function render($renderMode = null, $as = null) {
		// TYPO3 8.x
		if (version_compare(TYPO3_branch, '8.0', '>=')) {
			if (($result = parent::render($as)) !== '') {
				$this->preventCaching();
			}

			return $result;
		}

		// TYPO3 7.x
		if ($renderMode === NULL) {
			// Add defaults here as we need keep signature intact
			$renderMode = self::RENDER_MODE_DIV;
		}

		if (($result = parent::render($renderMode, $as)) !== '') {
			$this->preventCaching();
		}

		return $result;
	}

	/**
	 * Prevent caching if a flash message is displayed
	 *
	 * @todo Remove this! See https://github.com/fnagel/t3extblog/issues/112
	 *
	 * @return void
	 */
	protected function preventCaching() {
		if (isset($GLOBALS['TSFE']) && 
			(
				// @todo It seems that in TYPO3 8.1 (most likely in 8.0 too) getUserObjectType returns false
				version_compare(TYPO3_branch, '8.0', '>=') ||
				$this->contentObject->getUserObjectType() === ContentObjectRenderer::OBJECTTYPE_USER
			)
		) {
			$GLOBALS['TSFE']->no_cache = TRUE;
		}
	}

}
