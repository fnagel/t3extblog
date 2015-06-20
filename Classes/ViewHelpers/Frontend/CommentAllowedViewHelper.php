<?php

namespace TYPO3\T3extblog\ViewHelpers\Frontend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\T3extblog\Domain\Model\Post;

/**
 * ViewHelper
 *
 */
class CommentAllowedViewHelper extends AbstractConditionViewHelper {

	/**
	 * Check if a new comment is allowed
	 *
	 * @param Post $post
	 *
	 * @return string
	 */
	public function render(Post $post) {
		$settings = $this->templateVariableContainer->get('settings');

		if (!$settings['blogsystem']['comments']['allowed'] || $post->getAllowComments() === 1) {
			return $this->renderElseChild();
		}

		if ($post->getAllowComments() === 2 && !(isset($GLOBALS['TSFE']) && $GLOBALS['TSFE']->loginUser)) {
			return $this->renderElseChild();
		}

		if ($settings['blogsystem']['comments']['allowedUntil']) {
			if ($post->isExpired(trim($settings['blogsystem']['comments']['allowedUntil']))) {
				return $this->renderElseChild();
			}
		}

		return $this->renderThenChild();
	}
}