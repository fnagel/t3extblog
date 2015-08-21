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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * An URI Builder
 *
 * This a modfied version of the default extbase class which enables us to
 * use a FE link within a BE context
 */
class UriBuilder extends \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder {

	/**
	 * @var \TYPO3\T3extblog\Service\EnvironmentService
	 * @inject
	 */
	protected $environmentService;

	/**
	 * Life-cycle method that is called by the DI container as soon as this object is completely built
	 *
	 * @return void
	 */
	public function initializeObject() {
		parent::initializeObject();

		// @todo Remove this when TYPO3 6.2 is no longer relevant
		// Replace it by a simple inject annotation like above
		if (version_compare(TYPO3_branch, '7.0', '>')) {
			// This makes sure the settings are found correctly\
			// Seems there is no good reason but this would break TYPO3 6.2
			/* @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManagerInterface */
			$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
			$this->configurationManager = $objectManager->get('TYPO3\\T3extblog\\Configuration\\ConfigurationManager');
		}
	}

}