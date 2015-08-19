<?php

namespace TYPO3\T3extblog\Mvc\Web\Routing;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\T3extblog\Service\SettingsService;

/**
 * An URI Builder
 *
 * This a modfied version of the default extbase class which enables us to
 * use a FE link within a BE context
 */
class UriBuilder extends \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder {

	/**
	 * Builds the URI (always for frontend)
	 *
	 * @return string The URI
	 * @see buildFrontendUri()
	 */
	public function build() {
		return $this->buildFrontendUri();
	}

}