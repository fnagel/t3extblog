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
    protected static string $packageKey = 't3extblog';

    protected static ?string $extensionName = null;

    protected static string $localizationPrefix = 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:';

    /**
     * Register a plugin and hide default fields
     *
     */
    public static function registerPlugin(string $pluginName, string $pluginTitleKey)
    {
        // Register plugin
        ExtensionUtility::registerPlugin(self::$packageKey, $pluginName, self::$localizationPrefix . $pluginTitleKey);

        // Disable default fields
        self::disablePluginDefaultFields(self::getPluginSignature($pluginName));
    }

    
    public static function addFlexForm(string $pluginName, string $flexFormFilePath)
    {
        $pluginSignature = self::getPluginSignature($pluginName);

        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        ExtensionManagementUtility::addPiFlexFormValue(
            $pluginSignature,
            'FILE:EXT:'.self::$packageKey.$flexFormFilePath
        );
    }

    
    protected static function disablePluginDefaultFields(string $pluginSignature)
    {
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'pages, recursive';
    }

    
    protected static function getPluginSignature(string $pluginName): string
    {
        return strtolower(self::getExtensionName()).'_'.strtolower($pluginName);
    }

    
    protected static function getExtensionName(): string
    {
        if (self::$extensionName === null) {
            self::$extensionName = GeneralUtility::underscoredToUpperCamelCase(self::$packageKey);
        }

        return self::$extensionName;
    }
}
