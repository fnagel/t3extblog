<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Plugins
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'Blogsystem',
    array(
        'Post' => 'list, tag, category, author, show, permalink, preview',
        'Comment' => 'create, show',
    ),
    // non-cacheable actions
    array(
        'Post' => 'permalink, preview',
        'Comment' => 'create',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'Archive',
    array(
        'Post' => 'archive',
    ),
    // non-cacheable actions
    array(
        'Post' => '',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'Rss',
    array(
        'Post' => 'rss',
    ),
    // non-cacheable actions
    array(
        'Post' => '',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'SubscriptionManager',
    array(
        'Subscriber' => 'list, error, logout, confirm',
        'PostSubscriber' => 'list, delete, confirm',
        'BlogSubscriber' => 'list, delete, confirm, create',
    ),
    // non-cacheable actions
    array(
        'Subscriber' => 'list, error, logout, confirm',
        'PostSubscriber' => 'list, delete, confirm',
        'BlogSubscriber' => 'list, delete, confirm, create',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'BlogSubscription',
    array(
        'BlogSubscriberForm' => 'new, create, success',
    ),
    // non-cacheable actions
    array(
        'BlogSubscriberForm' => 'new, create, success',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'Categories',
    array(
        'Category' => 'list, show',
    ),
    // non-cacheable actions
    array(
        'Category' => '',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'LatestPosts',
    array(
        'Post' => 'latest',
    ),
    // non-cacheable actions
    array(
        'Post' => '',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'FelixNagel.'.$_EXTKEY,
    'LatestComments',
    array(
        'Comment' => 'latest',
    ),
    // non-cacheable actions
    array(
        'Comment' => '',
    )
);

if (TYPO3_MODE == 'BE') {
    // Add BE hooks
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        'FelixNagel\\T3extblog\\Hooks\\Tcemain';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
        'FelixNagel\\T3extblog\\Hooks\\Tcemain';

    // Install Tool Upgrade Wizard
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['t3extblog_preview'] =
        'FelixNagel\\T3extblog\\Updates\\PreviewUpdateWizard';
}

// add RealURL configuration
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['t3extblog'] =
    'EXT:t3extblog/Classes/Hooks/RealUrl.php:FelixNagel\\T3extblog\\Hooks\\RealUrl->extensionConfiguration';

// Support for dd_googlesitemap
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dd_googlesitemap']['sitemap']['t3extblog'] =
    'FelixNagel\\T3extblog\\Hooks\\Sitemap\\Generator->main';

// Add cHash configuration
// See: http://forum.typo3.org/index.php?t=msg&th=203350
$requiredParameters = array(
    'tx_t3extblog_blogsystem[action]',
    'tx_t3extblog_blogsystem[controller]',
    'tx_t3extblog_blogsystem[post]',
    'tx_t3extblog_blogsystem[permalinkPost]',
    'tx_t3extblog_blogsystem[previewPost]',
    'tx_t3extblog_blogsystem[tag]',
    'tx_t3extblog_blogsystem[category]',
    'tx_t3extblog_blogsystem[author]',
    'tx_t3extblog_blogsystem[@widget_0][currentPage]',
    'tx_t3extblog_subscriptionmanager[subscriber]',
);
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
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['avatarProviders']['defaultAvatarProvider'] = array(
    'provider' => 'TYPO3\\CMS\\Backend\\Backend\\Avatar\\DefaultAvatarProvider',
);

// Overwrite classes
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Extbase\\Configuration\\BackendConfigurationManager'] = array(
    'className' => 'FelixNagel\\T3extblog\\Configuration\\BackendConfigurationManager',
);

