<?php

namespace TYPO3\T3extblog\Service;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Service for determining environment params
 *
 * Modified for usage in custom UriBuilder to make sure we always produce nice FE links
 * Fools all checks we are in FE context.
 */
class EnvironmentService extends \TYPO3\CMS\Extbase\Service\EnvironmentService {

	/**
	 * Detects if TYPO3_MODE is defined and its value is "FE"
	 *
	 * @return boolean
	 */
	public function isEnvironmentInFrontendMode() {
		return TRUE;
	}

	/**
	 * Detects if TYPO3_MODE is defined and its value is "BE"
	 *
	 * @return bool
	 */
	public function isEnvironmentInBackendMode() {
		return FALSE;
	}
}
