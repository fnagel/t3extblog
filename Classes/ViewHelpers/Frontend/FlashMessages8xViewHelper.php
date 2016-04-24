<?php

namespace TYPO3\T3extblog\ViewHelpers\Frontend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Felix Nagel <info@felixnagel.com>
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

/**
 * View helper which renders the flash messages
 */
class FlashMessages8xViewHelper extends FlashMessagesViewHelper {

	/**
	 * Renders FlashMessages and flushes the FlashMessage queue
	 * Note: This disables the current page cache in order to prevent FlashMessage output
	 * from being cached.
	 *
	 * @see \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::no_cache
	 * @param string $as The name of the current flashMessage variable for rendering inside
	 * @return string rendered Flash Messages, if there are any.
	 * @api
	 */
	public function render($as = null) {
		return parent::render(null, $as);
	}

}
