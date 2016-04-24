<?php

namespace TYPO3\T3extblog\ViewHelpers\Frontend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2016 Felix Nagel <info@felixnagel.com>
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

use TYPO3\T3extblog\Utility\GeneralUtility;
use TYPO3\T3extblog\ViewHelpers\AbstractConditionViewHelper;
use TYPO3\T3extblog\Domain\Model\Post;

/**
 * ViewHelper
 */
class CommentAllowedViewHelper extends AbstractConditionViewHelper {

	/**
	 * @inheritdoc
	 */
	public function initializeArguments() {
		parent::initializeArguments();

		$this->registerArgument(
			'post',
			'TYPO3\\T3extblog\\Domain\\Model\\Post', 'Post object to check if new comments are allowed.',
			TRUE
		);
	}

	/**
	 * Check if a new comment is allowed
	 *
	 * @return string
	 */
	public function render() {
		$this->arguments['settings'] = $this->templateVariableContainer->get('settings');

		return parent::render();
	}

	/**
	 * This method decides if the condition is TRUE or FALSE
	 *
	 * @param array $arguments ViewHelper arguments to evaluate the condition
	 * @return bool
	 */
	static protected function evaluateCondition($arguments = null) {
		/* @var Post $post */
		$post = $arguments['post'];
		$settings = $arguments['settings'];

		if (!$settings['blogsystem']['comments']['allowed'] || $post->getAllowComments() === 1) {
			return FALSE;
		}

		if ($post->getAllowComments() === 2 && empty(GeneralUtility::getTsFe()->loginUser)) {
			return FALSE;
		}

		if ($settings['blogsystem']['comments']['allowedUntil']) {
			if ($post->isExpired(trim($settings['blogsystem']['comments']['allowedUntil']))) {
				return FALSE;
			}
		}

		return TRUE;
	}

}