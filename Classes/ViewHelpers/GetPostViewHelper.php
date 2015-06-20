<?php

namespace TYPO3\T3extblog\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Felix Kopp <felix-source@phorax.com>
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

/**
 * Issue command ViewHelper, see TYPO3 Core Engine method issueCommand
 *
 * @author Felix Kopp <felix-source@phorax.com>
 * @package TYPO3
 * @subpackage t3extblog
 */
class GetPostViewHelper extends AbstractBackendViewHelper {

	/**
	 * postRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\PostRepository
	 */
	protected $postRepository = NULL;

	/**
	 * @param int $uid
	 * @param boolean $respectEnableFields if set to false, hidden records are shown
	 *
	 * @return string
	 */
	public function render($uid = NULL, $respectEnableFields = TRUE) {
		if ($uid === NULL) {
			$uid = $this->renderChildren();
		}

		return $this->getPostRepository()->findByLocalizedUid($uid, $respectEnableFields);
	}

	/**
	 * @return \TYPO3\T3extblog\Domain\Repository\PostRepository
	 */
	protected function getPostRepository() {
		if ($this->postRepository === NULL) {
			$this->postRepository = GeneralUtility::makeInstance('TYPO3\\T3extblog\\Domain\\Repository\\PostRepository');
		}

		return $this->postRepository;
	}
}