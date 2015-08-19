<?php

namespace TYPO3\T3extblog\ViewHelpers\Frontend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Fluid\ViewHelpers\CObjectViewHelper;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\T3extblog\Domain\Model\AbstractLocalizedEntity;

/**
 * ViewHelper for rendering content
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class RenderContentViewHelper extends CObjectViewHelper {

	/**
	 * Render content
	 *
	 * @param ObjectStorage|array 	$contentElements
	 * @param int                   $index
	 * @param bool                  $removeMarker
	 * @param string                $typoscript
	 * @param string                $table
	 *
	 * @return string
	 */
	public function render(
		$contentElements, $index = 0, $removeMarker = TRUE, $typoscript = 'tt_content', $table = 'tt_content'
	) {
		$output = '';
		$iterator = 0;

		foreach ($contentElements as $content) {
			$iterator++;
			if (($iterator - 1) < $index) {
				continue;
			}

			// We need to make sure to get all (!) DB fields if its an object
			// Otherwise tt_content rendering will fail for plugins
			if (is_object($content) && $content instanceof AbstractLocalizedEntity) {
				$where = 'uid = ' . $content->getLocalizedUid();
				$where  .= $this->getEnableFields($table);

				$content = $this->getDatabase()->exec_SELECTgetSingleRow('*', $table, $where);
			}

			$output .= parent::render($typoscript, $content);
		}

		if ($removeMarker === TRUE) {
			$output = $this->removeMarker($output);
		}

		return $output;
	}

	/**
	 * Get database connection
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabase() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Get enable fields for DB where clause
	 *
	 * @param string $table
	 *
	 * @return string
	 */
	protected function getEnableFields($table) {
		if (TYPO3_MODE === 'FE') {
			$where = $GLOBALS['TSFE']->sys_page->enableFields($table);
		} else {
			$where = BackendUtility::BEenableFields($table);
		}

		return $where;
	}

	/**
	 * Remove marker
	 *
	 * @deprecated
	 * @param string $output
	 *
	 * @return string Rendered string
	 */
	protected function removeMarker($output) {
		return str_replace('###MORE###', '', $output);
	}
}