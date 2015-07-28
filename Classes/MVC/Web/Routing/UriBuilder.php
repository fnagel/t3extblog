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
use TYPO3\T3extblog\Service\SettingsService;

/**
 * An URI Builder
 *
 * This a modfied version of the default extbase class which enables us to
 * use a FE link within a BE context
 */
class UriBuilder extends \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder {

	/**
	 * @var \TYPO3\T3extblog\Service\SettingsService
	 */
	protected $settingsService;

	/**
	 * Injects the Settings Service
	 *
	 * @param \TYPO3\T3extblog\Service\SettingsService $settingsService
	 *
	 * @return void
	 */
	public function injectSettingsService(SettingsService $settingsService) {
		$this->settingsService = $settingsService;
	}

	/**
	 * Creates an URI used for linking to an Extbase action.
	 * Works in Frontend and Backend mode of TYPO3.
	 *
	 * @param string $actionName Name of the action to be called
	 * @param array $controllerArguments Additional query parameters. Will be "namespaced" and merged with $this->arguments.
	 * @param string $controllerName Name of the target controller. If not set, current ControllerName is used.
	 * @param string $extensionName Name of the target extension, without underscores. If not set, current ExtensionName is used.
	 * @param string $pluginName Name of the target plugin. If not set, current PluginName is used.
	 * @return string the rendered URI
	 * @api
	 * @see build()
	 */
	public function uriFor($actionName = NULL, $controllerArguments = array(), $controllerName, $extensionName, $pluginName) {
		if ($actionName !== NULL) {
			$controllerArguments['action'] = $actionName;
		}

		$controllerArguments['controller'] = $controllerName;

		if ($this->isFeatureEnabled('skipDefaultArguments')) {
			$controllerArguments = $this->removeDefaultControllerAndAction($controllerArguments, $extensionName, $pluginName);
		}

		if ($this->targetPageUid === NULL) {
			$this->targetPageUid = $this->extensionService->getTargetPidByPlugin($extensionName, $pluginName);
		}

		if ($this->format !== '') {
			$controllerArguments['format'] = $this->format;
		}

		if ($this->argumentPrefix !== NULL) {
			$prefixedControllerArguments = array($this->argumentPrefix => $controllerArguments);
		} else {
			$pluginNamespace = $this->extensionService->getPluginNamespace($extensionName, $pluginName);
			$prefixedControllerArguments = array($pluginNamespace => $controllerArguments);
		}
		$this->arguments = GeneralUtility::array_merge_recursive_overrule($this->arguments, $prefixedControllerArguments);

		return $this->buildFrontendUri();
	}

	/**
	 * Returns TRUE if a certain feature, identified by $featureName
	 * should be activated, FALSE for backwards-compatible behavior.
	 *
	 * This is an INTERNAL API used throughout Extbase and Fluid for providing backwards-compatibility.
	 * Do not use it in your custom code!
	 *
	 * @param string $featureName
	 * @return boolean
	 */
	public function isFeatureEnabled($featureName) {
		$configuration = $this->settingsService->getFrameworkSettings();
		return (boolean)(isset($configuration['features'][$featureName]) && $configuration['features'][$featureName]);
	}

}