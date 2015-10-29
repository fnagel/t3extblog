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

use TYPO3\T3extblog\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\T3extblog\Domain\Model\AbstractLocalizedEntity;

/**
 * ViewHelper for rendering content
 */
class RenderContentViewHelper extends AbstractViewHelper {

	/**
	 * Render content
	 *
	 * @param ObjectStorage|array 	$contentElements
	 * @param int                   $index
	 * @param bool                  $removeMarker
	 * @param string                $table
	 *
	 * @return string
	 */
	public function render($contentElements, $index = 0, $removeMarker = TRUE, $table = 'tt_content') {
		$output = '';
		$iterator = 0;

		foreach ($contentElements as $content) {
			$iterator++;
			if (($iterator - 1) < $index) {
				continue;
			}

			$uid = $this->getElementUid($content);
			if ($uid === NULL) {
				continue;
			}

			$output .= $this->renderRecord($uid, $table);
		}

		if ($removeMarker === TRUE) {
			$output = $this->removeMarker($output);
		}

		return $output;
	}

	/**
	 * This function renders a raw record into the corresponding
	 * element by typoscript RENDER function
	 *
	 * Taken from EXT:vhs/Classes/ViewHelpers/Content/AbstractContentViewHelper.php
	 *
	 * @param int $uid
	 * @param string $table
	 * @return string|NULL
	 */
	protected function renderRecord($uid, $table) {
		if (0 < GeneralUtility::getTsFe()->recordRegister[$table . ':' . $uid]) {
			return NULL;
		}

		$configuration = array(
			'tables' => $table,
			'source' => $uid,
			'dontCheckPid' => 1
		);

		$parent = GeneralUtility::getTsFe()->currentRecord;
		if (FALSE === empty($parent)) {
			GeneralUtility::getTsFe()->recordRegister[$parent]++;
		}

		$html = $this->getContentObjectRenderer()->cObjGetSingle('RECORDS', $configuration);

		GeneralUtility::getTsFe()->currentRecord = $parent;
		if (FALSE === empty($parent)) {
			GeneralUtility::getTsFe()->recordRegister[$parent]--;
		}

		return $html;
	}

	/**
	 * Get element uid
	 *
	 * @param array|DomainObjectInterface $element
	 *
	 * @return int|NULL
	 */
	protected function getElementUid($element) {
		if ($element instanceof DomainObjectInterface) {

			if ($element instanceof AbstractLocalizedEntity) {
				return (int) $element->getLocalizedUid();
			}

			return (int) $element->getUid();
		}

		if (is_array($element) && isset($element['uid'])) {
			return (int) $element['uid'];
		}

		return NULL;
	}

	/**
	 * Get content object renderer
	 *
	 * @return \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected function getContentObjectRenderer() {
		return GeneralUtility::getTsFe()->cObj;
	}

	/**
	 * Remove marker
	 *
	 * @todo Remove this in version 3.0.0
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