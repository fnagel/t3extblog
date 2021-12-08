<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

$packageKey = 't3extblog';

// Add static TS
ExtensionManagementUtility::addStaticFile(
    $packageKey,
    'Configuration/TypoScript',
    'T3Extblog: Default setup (needed)'
);

ExtensionManagementUtility::addStaticFile(
    $packageKey,
    'Configuration/TypoScript/Rss',
    'T3Extblog: RSS setup'
);

if (ExtensionManagementUtility::isLoaded('seo')) {
    ExtensionManagementUtility::addStaticFile(
        $packageKey,
        'Configuration/TypoScript/Sitemap',
        'T3Extblog: Sitemap setup'
    );
}

// Twitter Bootstrap 4
ExtensionManagementUtility::addStaticFile(
    $packageKey,
    'Configuration/TypoScript/TwitterBootstrap4',
    'T3Extblog: Twitter Bootstrap 4 templates'
);
