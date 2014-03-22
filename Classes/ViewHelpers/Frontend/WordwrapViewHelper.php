<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Georg Ringer <typo3@ringerge.org>
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
 * ViewHelper PHP wordwrap
 *
 */
class Tx_T3extblog_ViewHelpers_Frontend_WordwrapViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Php wordwrap function VH
	 *
	 * @param boolean $prepend
	 *
	 * @return void
	 */
	public function render($width = 75) {
		$content = $this->renderChildren();

		return wordwrap($content, $width);
	}
}

?>