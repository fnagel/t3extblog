<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use FelixNagel\T3extblog\Controller\PostController;
use FelixNagel\T3extblog\Controller\CommentController;
use FelixNagel\T3extblog\Controller\SubscriberController;
use FelixNagel\T3extblog\Controller\PostSubscriberController;
use FelixNagel\T3extblog\Controller\BlogSubscriberController;
use FelixNagel\T3extblog\Controller\BlogSubscriberFormController;
use FelixNagel\T3extblog\Controller\CategoryController;
use FelixNagel\T3extblog\Hooks\Tcemain;
use TYPO3\CMS\Backend\Backend\Avatar\DefaultAvatarProvider;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use FelixNagel\T3extblog\Routing\Aspect\PostMapper;
use FelixNagel\T3extblog\Routing\Aspect\PostTagMapper;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\FileWriter;

defined('TYPO3') || die();

// Plugins
ExtensionUtility::configurePlugin(
    'T3extblog',
    'Blogsystem',
    [
        PostController::class => 'list, tag, category, author, show, permalink, preview',
        CommentController::class => 'create, show',
    ],
    // non-cacheable actions
    [
        PostController::class => 'permalink, preview',
        CommentController::class => 'create',
    ]
);

ExtensionUtility::configurePlugin(
    'T3extblog',
    'Archive',
    [
        PostController::class => 'archive',
    ],
    // non-cacheable actions
    [
        PostController::class => '',
    ]
);

ExtensionUtility::configurePlugin(
    'T3extblog',
    'Rss',
    [
        PostController::class => 'rss',
    ],
    // non-cacheable actions
    [
        PostController::class => '',
    ]
);

ExtensionUtility::configurePlugin(
    'T3extblog',
    'SubscriptionManager',
    [
        SubscriberController::class => 'list, error, logout',
        PostSubscriberController::class => 'list, delete, confirm',
        BlogSubscriberController::class => 'list, delete, confirm, create',
    ],
    // non-cacheable actions
    [
        SubscriberController::class => 'list, error, logout',
        PostSubscriberController::class => 'list, delete, confirm',
        BlogSubscriberController::class => 'list, delete, confirm, create',
    ]
);

ExtensionUtility::configurePlugin(
    'T3extblog',
    'BlogSubscription',
    [
        BlogSubscriberFormController::class => 'new, create, success',
    ],
    // non-cacheable actions
    [
        BlogSubscriberFormController::class => 'new, create, success',
    ]
);

ExtensionUtility::configurePlugin(
    'T3extblog',
    'Categories',
    [
        CategoryController::class => 'list, show',
    ],
    // non-cacheable actions
    [
        CategoryController::class => '',
    ]
);

ExtensionUtility::configurePlugin(
    'T3extblog',
    'LatestPosts',
    [
        PostController::class => 'latest',
    ],
    // non-cacheable actions
    [
        PostController::class => '',
    ]
);

ExtensionUtility::configurePlugin(
    'T3extblog',
    'LatestComments',
    [
        CommentController::class => 'latest',
    ],
    // non-cacheable actions
    [
        CommentController::class => '',
    ]
);

// Add BE hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = Tcemain::class;
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = Tcemain::class;

// Add cHash configuration
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['requireCacheHashPresenceParameters'] =
    array_merge($GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['requireCacheHashPresenceParameters'], [
        'tx_t3extblog_blogsystem[controller]',
        'tx_t3extblog_blogsystem[action]',
        'tx_t3extblog_blogsystem[post]',
        'tx_t3extblog_blogsystem[permalinkPost]',
        'tx_t3extblog_blogsystem[previewPost]',
        'tx_t3extblog_blogsystem[tag]',
        'tx_t3extblog_blogsystem[category]',
        'tx_t3extblog_blogsystem[author]',
        'tx_t3extblog_blogsystem[page]',

        'tx_t3extblog_subscriptionmanager[controller]',
        'tx_t3extblog_subscriptionmanager[action]',
        'tx_t3extblog_subscriptionmanager[subscriber]',
    ]);
$GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'] =
    array_merge($GLOBALS['TYPO3_CONF_VARS']['FE']['cacheHash']['excludedParameters'], [
        'tx_t3extblog_subscriptionmanager[code]',
    ]);

// Make default avatar provider available in FE
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['avatarProviders']['defaultAvatarProvider'] = [
    'provider' => DefaultAvatarProvider::class,
];

// Routing
$GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['T3extblogPostMapper'] = PostMapper::class;
$GLOBALS['TYPO3_CONF_VARS']['SYS']['routing']['aspects']['T3extblogPostTagMapper'] = PostTagMapper::class;

// Logging
$GLOBALS['TYPO3_CONF_VARS']['LOG']['FelixNagel']['T3extblog']['writerConfiguration'] ??= [
    (Environment::getContext()->isDevelopment() ? LogLevel::DEBUG : LogLevel::ERROR) => [
        FileWriter::class => [
            'logFileInfix' => 't3extblog',
        ],
    ],
];
