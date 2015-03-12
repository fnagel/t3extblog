<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014-2015 Felix Nagel <info@felixnagel.com>
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
 * This class contains a renderer for the 'news' sitemap.
 *
 * @author	Felix Nagel <info@felixnagel.com>
 * @package	TYPO3
 * @subpackage	t3extblog
 */
class Tx_T3extblog_Hooks_Sitemap_Renderer extends tx_ddgooglesitemap_news_renderer {

	/**
	 * Creates an instance of this class
	 */
	public function __construct() {
		if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_t3extblog.']['settings.']['blogName']) {
			$this->sitename = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_t3extblog.']['settings.']['blogName'];
		} else {
			$this->sitename = $GLOBALS['TSFE']->tmpl->setup['sitetitle'];
		}

		$this->sitename = htmlspecialchars($this->sitename);
	}

}
