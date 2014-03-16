<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2014 Felix Nagel <info@felixnagel.com>
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
 * ViewHelper
 *
 */
class Tx_T3extblog_ViewHelpers_Frontend_CommentAllowedViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * Check if a new comment is allowed
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post
	 *
	 * @return boolean
	 */
	public function render($post) {
		$settings = $this->templateVariableContainer->get('settings');
		$condition = TRUE;

		if (!($settings['blogsystem']['comments']['allowed'] && $post->getAllowComments() === 0)) {
			$condition = FALSE;
		}

		if ($settings['blogsystem']['comments']["allowedUntil"]) {
			if ($post->isExpired(trim($settings['blogsystem']['comments']["allowedUntil"]))) {
				$condition = FALSE;
			}
		}

		if ($condition) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}
}

?>