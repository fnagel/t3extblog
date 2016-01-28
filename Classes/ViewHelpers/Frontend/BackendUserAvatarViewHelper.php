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

use TYPO3\CMS\Backend\Backend\Avatar\Image;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Get avatar for backend user
 */
class BackendUserAvatarViewHelper extends AbstractViewHelper {

	/**
	 * Render the avatar image
	 *
	 * @param int $uid
	 * @param int $size
	 * @param string $default
	 *
	 * @return string The image URL
	 */
	public function render($uid, $size = 32, $default = NULL) {
		$url = $this->getAvatarUrl($uid, $size);

		if ($url !== NULL) {
			return $url;
		}

		return $this->noAvatarFound($default);
	}

	/**
	 * Get avatar url using TYPO3 avatar provider
	 *
	 * @param int $uid
	 * @param int $size
	 *
	 * @return string|NULL
	 */
	protected function getAvatarUrl($uid, $size) {
		// @todo Remove this when 6.2 is no longer relevant
		if (version_compare(TYPO3_branch, '7.5', '>=')) {
			$avatarImage = $this->getAvatarImage($uid, $size);

			if ($avatarImage !== NULL) {
				return $avatarImage->getUrl(TRUE);
			}
		}

		return NULL;
	}

	/**
	 * Get avatar image using TYPO3 avatar provider
	 *
	 * @param int $uid
	 * @param int $size
	 *
	 * @return Image|NULL
	 */
	protected function getAvatarImage($uid, $size) {
		$backendUser = $this->getDatabase()->exec_SELECTgetSingleRow('*', 'be_users', 'uid=' . (int) $uid);

		/** @var \TYPO3\CMS\Backend\Backend\Avatar\Avatar $avatar */
		$avatar = GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Backend\\Avatar\\Avatar');

		return $avatar->getImage($backendUser, $size);
	}

	/**
	 * Called when no user avatar has been found
	 *
	 * @param string $default Blank gif als fallback
	 *
	 * @return string
	 */
	protected function noAvatarFound($default = NULL) {
		if ($default === NULL) {
			$default = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
		}

		return $default;
	}

	/**
	 * Get database connection
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabase() {
		return $GLOBALS['TYPO3_DB'];
	}
}
