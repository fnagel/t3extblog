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
     */
    public static function registerPlugin(string $pluginName, string $localizationKey): string
    {
        return ExtensionUtility::registerPlugin(
            self::$packageKey,
            $pluginName,
            self::$localizationPrefix.$localizationKey.'.title',
            'extensions-t3extblog-plugin',
            'blog',
            self::$localizationPrefix.$localizationKey.'.description',
        );
    }

    public static function addFlexForm(string $contentTypeName, string $flexFormFilePath): void
    {
        // Add the FlexForm
        ExtensionManagementUtility::addPiFlexFormValue(
            '*',
            'FILE:EXT:'.self::$packageKey.$flexFormFilePath,
            $contentTypeName
        );

        // Add the FlexForm to the show item list
        ExtensionManagementUtility::addToAllTCAtypes(
            'tt_content',
            '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:plugin, pi_flexform',
            $contentTypeName,
            'after:palette:headers'
        );
    }

    protected static function getExtensionName(): string
    {
        if (self::$extensionName === null) {
            self::$extensionName = GeneralUtility::underscoredToUpperCamelCase(self::$packageKey);
        }

        return self::$extensionName;
    }
}
