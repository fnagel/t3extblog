<?php

namespace TYPO3\T3extblog\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Sebastian Schreiber <me@schreibersebastian.de >
 *  (c) 2010 Georg Ringer <typo3@ringerge.org>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Provide a way to get the configuration just everywhere.
 */
class SettingsService
{
    /**
     * Extension name.
     *
     * Needed as parameter for configurationManager->getConfiguration when used in BE context
     * Otherwise generated TS will be incorrect or missing
     *
     * @var string
     */
    protected $extensionName = 't3extblog';

    /**
     * Plugin name.
     *
     * Needed as parameter for configurationManager->getConfiguration when used in BE context
     * Otherwise generated TS will be incorrect or missing when used in BE
     *
     * @var string
     */
    protected $pluginName = '';

    /**
     * @var mixed
     */
    protected $typoScriptSettings = null;

    /**
     * @var mixed
     */
    protected $frameworkSettings = null;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Extbase\Service\TypoScriptService
     * @inject
     */
    protected $typoScriptService;

    /**
     * Returns all framework settings.
     *
     * @return array
     *
     * @throws Exception
     */
    public function getFrameworkSettings()
    {
        if ($this->frameworkSettings === null) {
            $this->frameworkSettings = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                $this->extensionName,
                $this->pluginName
            );
        }

        if ($this->frameworkSettings === null) {
            throw new Exception('No framework typoscript settings available.');
        }

        return $this->frameworkSettings;
    }

    /**
     * Returns all TS settings.
     *
     * @return array
     *
     * @throws Exception
     */
    public function getTypoScriptSettings()
    {
        if ($this->typoScriptSettings === null) {
            $this->typoScriptSettings = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                $this->extensionName,
                $this->pluginName
            );
        }

        if ($this->typoScriptSettings === null) {
            throw new Exception('No typoscript settings available.');
        }

        return $this->typoScriptSettings;
    }

    /**
     * Get full typoscript configuration.
     *
     * @return array
     */
    protected function getFullTypoScriptConfig()
    {
        $setup = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );

        return $this->typoScriptService->convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_t3extblog.']);
    }

    /**
     * Returns the settings at path $path, which is separated by ".",
     * e.g. "pages.uid".
     * "pages.uid" would return $this->settings['pages']['uid'].
     *
     * If the path is invalid or no entry is found, false is returned.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function getTypoScriptByPath($path)
    {
        return ObjectAccess::getPropertyPath($this->getTypoScriptSettings(), $path);
    }

    /**
     * Set storage pid in BE.
     *
     * Only needed when the class is called or injected in a BE context, e.g. a hook.
     * Without the generation of the TS is based upon the next root page (default
     * extbase behaviour) and repositories won't work as expected.
     *
     * @param $pageUid
     */
    public function setPageUid($pageUid)
    {
        if (TYPO3_MODE === 'BE') {
            $currentPid['persistence']['storagePid'] = (int) $pageUid;
            $this->configurationManager->setConfiguration(array_merge($this->getFrameworkSettings(), $currentPid));
            GeneralUtility::_GETset((int) $pageUid, 'id');
        }
    }
}
