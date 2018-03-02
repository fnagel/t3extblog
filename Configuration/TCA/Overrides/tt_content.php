<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$packageKey = 't3extblog';
$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($packageKey);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $packageKey,
    'Blogsystem',
    'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:blogsystem.title'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $packageKey,
    'SubscriptionManager',
    'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:subscriptionmanager.title'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $packageKey,
    'BlogSubscription',
    'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:blogsubscription.title'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $packageKey,
    'Archive',
    'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:archive.title'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $packageKey,
    'Rss',
    'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:rss.title'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $packageKey,
    'Categories',
    'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:categories.title'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $packageKey,
    'LatestPosts',
    'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:latestposts.title'
);
$pluginSignature = strtolower($extensionName).'_latestposts';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform,recursive';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    $pluginSignature,
    'FILE:EXT:'.$packageKey.'/Configuration/FlexForms/LatestPosts.xml'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $packageKey,
    'LatestComments',
    'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:latestcomments.title'
);
