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

use TYPO3\T3extblog\ViewHelpers\AbstractConditionViewHelper;

/**
 * ViewHelper to render children only for specific versions
 */
class Typo3VersionViewHelper extends AbstractConditionViewHelper {

	/**
	 * Render children if version matches
	 *
	 * @param string $version
	 * @param string $operator
	 *
	 * @return string
	 */
	public function render($version = '6.2', $operator = '>') {
		return parent::render();
	}

	/**
	 * This method decides if the condition is TRUE or FALSE
	 *
	 * @param array $arguments ViewHelper arguments to evaluate the condition
	 * @return bool
	 */
	static protected function evaluateCondition($arguments = NULL) {
		$version = $arguments['version'];
		$operator = $arguments['operator'];

		if (version_compare(TYPO3_branch, $version, $operator)) {
			return TRUE;
		}

		return FALSE;
	}

}
