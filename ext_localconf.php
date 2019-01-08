<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add page TS config
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:t3extblog/Configuration/TSconfig/Page.ts">'
);

// Plugins
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'Blogsystem',
    [
        'Post' => 'list, tag, category, author, show, permalink, preview',
        'Comment' => 'create, show',
    ],
    // non-cacheable actions
    [
        'Post' => 'permalink, preview',
        'Comment' => 'create',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'Archive',
    [
        'Post' => 'archive',
    ],
    // non-cacheable actions
    [
        'Post' => '',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'Rss',
    [
        'Post' => 'rss',
    ],
    // non-cacheable actions
    [
        'Post' => '',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'SubscriptionManager',
    [
        'Subscriber' => 'list, error, logout, confirm',
        'PostSubscriber' => 'list, delete, confirm',
        'BlogSubscriber' => 'list, delete, confirm, create',
    ],
    // non-cacheable actions
    [
        'Subscriber' => 'list, error, logout, confirm',
        'PostSubscriber' => 'list, delete, confirm',
        'BlogSubscriber' => 'list, delete, confirm, create',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'BlogSubscription',
    [
        'BlogSubscriberForm' => 'new, create, success',
    ],
    // non-cacheable actions
    [
        'BlogSubscriberForm' => 'new, create, success',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'Categories',
    [
        'Category' => 'list, show',
    ],
    // non-cacheable actions
    [
        'Category' => '',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'LatestPosts',
    [
        'Post' => 'latest',
    ],
    // non-cacheable actions
    [
        'Post' => '',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'LatestComments',
    [
        'Comment' => 'latest',
    ],
    // non-cacheable actions
    [
        'Comment' => '',
    ]
);

if (TYPO3_MODE == 'BE') {
    // Add BE hooks
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        \FelixNagel\T3extblog\Hooks\Tcemain::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
        \FelixNagel\T3extblog\Hooks\Tcemain::class;

    // Install Tool Upgrade Wizard
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update'][\FelixNagel\T3extblog\Updates\PreviewUpdateWizard::class] =
        \FelixNagel\T3extblog\Updates\PreviewUpdateWizard::class;
}

// Add cHash configuration
// See: http://forum.typo3.org/index.php?t=msg&th=203350
$requiredParameters = [
    'tx_t3extblog_blogsystem[controller]',
    'tx_t3extblog_blogsystem[action]',
    'tx_t3extblog_blogsystem[post]',
    'tx_t3extblog_blogsystem[permalinkPost]',
    'tx_t3extblog_blogsystem[previewPost]',
    'tx_t3extblog_blogsystem[tag]',
    'tx_t3extblog_blogsystem[category]',
    'tx_t3extblog_blogsystem[author]',
    'tx_t3extblog_blogsystem[@widget_0][currentPage]',
    'tx_t3extblog_subscriptionmanager[subscriber]',
];
$GLOBALS['TYPO3_CONF_VARS']['FE']['cHashRequiredParameters'] .= ','.implode(',', $requiredParameters);

// Make sure post preview works, taken from EXT:tt_news
$configuredCookieName = trim($GLOBALS['TYPO3_CONF_VARS']['BE']['cookieName']);
if (empty($configuredCookieName)) {
    $configuredCookieName = 'be_typo_user';
}
if ($_COOKIE[$configuredCookieName]) {
    $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFoundOnCHashError'] = 0;
}

// Make default avatar provider available in FE
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['avatarProviders']['defaultAvatarProvider'] = [
    'provider' => \TYPO3\CMS\Backend\Backend\Avatar\DefaultAvatarProvider::class,
];

// Overwrite classes
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Extbase\\Configuration\\BackendConfigurationManager'] = [
    'className' => \FelixNagel\T3extblog\Configuration\BackendConfigurationManager::class,
];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Core\\Routing\\PageRouter'] = [
    'className' => \FelixNagel\T3extblog\Routing\PageRouter::class,
];
