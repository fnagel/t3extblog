<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2014 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * ViewHelper for rendering content
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_ViewHelpers_Frontend_RenderContentViewHelper extends Tx_T3extblog_ViewHelpers_Frontend_BaseRenderViewHelper {

	/**
	 * Render content
	 *
	 * @param array $contentElements
	 * @param int $index
	 * @param bool $removeMarker
	 * @param string $typoscript
	 *
	 * @return string
	 */
	public function render($contentElements, $index = 0, $removeMarker = TRUE, $typoscript = 'tt_content') {
		$output = "";
		$iterator = 0;

		foreach ($contentElements as $content) {
			$iterator++;
			if (($iterator - 1) < $index) {
				continue;
			}

			$output .= $this->renderContentElement($content, $typoscript);
		}

		if ($removeMarker === TRUE) {
			$output = $this->removeMarker($output);
		}

		return $output;
	}
}

?>