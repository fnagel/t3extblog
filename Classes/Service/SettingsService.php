<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
    protected $extensionName = 'T3extblog';

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
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $configurationManager;

    /**
     * Legacy alias of \TYPO3\CMS\Core\TypoScript\TypoScriptService
     *
     * @var \TYPO3\CMS\Extbase\Service\TypoScriptService
     * @TYPO3\CMS\Extbase\Annotation\Inject
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
}
