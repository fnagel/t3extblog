<?php

namespace FelixNagel\T3extblog\Utility;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * TCA utility class.
 */
class TcaUtility implements SingletonInterface
{
    protected static $packageKey = 't3extblog';

    protected static $extensionName = null;

    protected static $localizationPrefix = 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:';

    /**
     * Register a plugin and hide default fields
     *
     * @param string $pluginName
     * @param string $pluginTitleKey
     */
    public static function registerPlugin($pluginName, $pluginTitleKey)
    {
        // Register plugin
        ExtensionUtility::registerPlugin(self::$packageKey, $pluginName, self::$localizationPrefix . $pluginTitleKey);

        // Disable default fields
        self::disablePluginDefaultFields(self::getPluginSignature($pluginName));
    }

    /**
     * @param string $pluginName
     * @param string $flexFormFilePath
     */
    public static function addFlexForm($pluginName, $flexFormFilePath)
    {
        $pluginSignature = self::getPluginSignature($pluginName);

        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        ExtensionManagementUtility::addPiFlexFormValue(
            $pluginSignature,
            'FILE:EXT:'.self::$packageKey.$flexFormFilePath
        );
    }

    /**
     * @param string $pluginSignature
     */
    protected static function disablePluginDefaultFields($pluginSignature)
    {
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'pages, recursive';
    }

    /**
     * @param string $pluginName
     *
     * @return string
     */
    protected static function getPluginSignature($pluginName)
    {
        return strtolower(self::getExtensionName()).'_'.strtolower($pluginName);
    }

    /**
     * @return string
     */
    protected static function getExtensionName()
    {
        if (self::$extensionName === null) {
            self::$extensionName = GeneralUtility::underscoredToUpperCamelCase(self::$packageKey);
        }

        return self::$extensionName;
    }
}
