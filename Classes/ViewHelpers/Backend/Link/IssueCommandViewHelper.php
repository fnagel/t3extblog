<?php

namespace TYPO3\T3extblog\ViewHelpers\Backend\Link;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A view helper for creating edit on click links
 */
class IssueCommandViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
	}

	/**
	 * Returns a link with a command to TYPO3 Core Engine (tce_db.php)
	 *
	 * @see DocumentTemplate::issueCommand()
	 *
	 * @param string $parameters
	 * @param string|int $redirectUrl
	 * @return string
	 */
	public function render($parameters, $redirectUrl = '') {
		if (version_compare(TYPO3_branch, '7.0', '<')) {
			/** @var $documentTemplate \TYPO3\CMS\Backend\Template\DocumentTemplate */
			$documentTemplate = GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Template\\DocumentTemplate');
			$href = $documentTemplate->issueCommand('&' . $parameters, $redirectUrl);
		} else {
			$href = BackendUtility::getLinkToDataHandlerAction('&' . $parameters, $redirectUrl);
		}

		$this->tag->addAttribute('href', $href);
		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(TRUE);

		return $this->tag->render();
	}

}
