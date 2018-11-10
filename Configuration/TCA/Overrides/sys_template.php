<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$packageKey = 't3extblog';

// Add static TS
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $packageKey,
    'Configuration/TypoScript',
    'T3Extblog: Default setup (needed)'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $packageKey,
    'Configuration/TypoScript/Rss',
    'T3Extblog: Rss setup'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $packageKey,
    'Configuration/TypoScript/RealUrl',
    'T3Extblog: additional RealUrl config'
);
