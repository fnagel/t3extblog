<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Add page TS config
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="DIR:EXT:t3extblog/Configuration/TSconfig/" extensions="tsconfig">'
);

// Plugins
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3extblog',
    'Blogsystem',
    [
        \FelixNagel\T3extblog\Controller\PostController::class => 'list, tag, category, author, show, permalink, preview',
        \FelixNagel\T3extblog\Controller\CommentController::class => 'create, show',
    ],
    // non-cacheable actions
    [
        \FelixNagel\T3extblog\Controller\PostController::class => 'permalink, preview',
        \FelixNagel\T3extblog\Controller\CommentController::class => 'create',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3extblog',
    'Archive',
    [
        \FelixNagel\T3extblog\Controller\PostController::class => 'archive',
    ],
    // non-cacheable actions
    [
        \FelixNagel\T3extblog\Controller\PostController::class => '',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3extblog',
    'Rss',
    [
        \FelixNagel\T3extblog\Controller\PostController::class => 'rss',
    ],
    // non-cacheable actions
    [
        \FelixNagel\T3extblog\Controller\PostController::class => '',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3extblog',
    'SubscriptionManager',
    [
        \FelixNagel\T3extblog\Controller\SubscriberController::class => 'list, error, logout',
        \FelixNagel\T3extblog\Controller\PostSubscriberController::class => 'list, delete, confirm',
        \FelixNagel\T3extblog\Controller\BlogSubscriberController::class => 'list, delete, confirm, create',
    ],
    // non-cacheable actions
    [
        \FelixNagel\T3extblog\Controller\SubscriberController::class => 'list, error, logout',
        \FelixNagel\T3extblog\Controller\PostSubscriberController::class => 'list, delete, confirm',
        \FelixNagel\T3extblog\Controller\BlogSubscriberController::class => 'list, delete, confirm, create',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3extblog',
    'BlogSubscription',
    [
        \FelixNagel\T3extblog\Controller\BlogSubscriberFormController::class => 'new, create, success',
    ],
    // non-cacheable actions
    [
        \FelixNagel\T3extblog\Controller\BlogSubscriberFormController::class => 'new, create, success',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3extblog',
    'Categories',
    [
        \FelixNagel\T3extblog\Controller\CategoryController::class => 'list, show',
    ],
    // non-cacheable actions
    [
        \FelixNagel\T3extblog\Controller\CategoryController::class => '',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3extblog',
    'LatestPosts',
    [
        \FelixNagel\T3extblog\Controller\PostController::class => 'latest',
    ],
    // non-cacheable actions
    [
        \FelixNagel\T3extblog\Controller\PostController::class => '',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'T3extblog',
    'LatestComments',
    [
        \FelixNagel\T3extblog\Controller\CommentController::class => 'latest',
    ],
    // non-cacheable actions
    [
        \FelixNagel\T3extblog\Controller\CommentController::class => '',
    ]
);

if (TYPO3_MODE == 'BE') {
    // Add BE hooks
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
        \FelixNagel\T3extblog\Hooks\Tcemain::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] =
        \FelixNagel\T3extblog\Hooks\Tcemain::class;
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

    'tx_t3extblog_subscriptionmanager[controller]',
    'tx_t3extblog_subscriptionmanager[action]',
    'tx_t3extblog_subscriptionmanager[subscriber]',
    'tx_t3extblog_subscriptionmanager[code]',
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
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager::class] = [
    'className' => \FelixNagel\T3extblog\Configuration\BackendConfigurationManager::class,
];

// Routing
$GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['T3extblogPostMapper'] =
    \FelixNagel\T3extblog\Routing\Aspect\PostMapper::class;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['T3extblogPostTagMapper'] =
    \FelixNagel\T3extblog\Routing\Aspect\PostTagMapper::class;

// Logging
$logLevel = \TYPO3\CMS\Core\Core\Environment::getContext()->isDevelopment() ?
    \TYPO3\CMS\Core\Log\LogLevel::DEBUG : \TYPO3\CMS\Core\Log\LogLevel::ERROR;
$GLOBALS['TYPO3_CONF_VARS']['LOG']['FelixNagel']['T3extblog']['writerConfiguration'] = [
    $logLevel => [
        \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
            'logFileInfix' => 't3extblog',
        ],
    ],
];
