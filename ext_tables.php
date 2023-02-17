<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use FelixNagel\T3extblog\Controller\BackendDashboardController;
use FelixNagel\T3extblog\Controller\BackendPostController;
use FelixNagel\T3extblog\Controller\BackendCommentController;
use FelixNagel\T3extblog\Controller\BackendSubscriberController;

defined('TYPO3') || die();

call_user_func(static function ($packageKey) {
    ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_post');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_cat');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_com');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_com_nl');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_blog_nl');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_pingback');
    ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_trackback');

    // Add icons to registry
    /* @var $iconRegistry IconRegistry */
    $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
    $iconRegistry->registerIcon(
        'extensions-t3extblog-post',
        BitmapIconProvider::class,
        ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/page.png']
    );
    $iconRegistry->registerIcon(
        'extensions-t3extblog-category',
        BitmapIconProvider::class,
        ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/category.png']
    );
    $iconRegistry->registerIcon(
        'extensions-t3extblog-comment',
        BitmapIconProvider::class,
        ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/comment.png']
    );
    $iconRegistry->registerIcon(
        'extensions-t3extblog-subscriber',
        BitmapIconProvider::class,
        ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/subscriber.png']
    );
    $iconRegistry->registerIcon(
        'extensions-t3extblog-trackback',
        BitmapIconProvider::class,
        ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/trackback.png']
    );
    $iconRegistry->registerIcon(
        'extensions-t3extblog-plugin',
        BitmapIconProvider::class,
        ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/module.png']
    );
    $iconRegistry->registerIcon(
        'tcarecords-pages-contains-t3blog',
        BitmapIconProvider::class,
        ['source' => 'EXT:t3extblog/Resources/Public/Icons/folder.png']
    );

    // Register  Backend Module
    ExtensionUtility::registerModule(
        'T3extblog',
        'web',
        'Tx_T3extblog',
        '',
        [
            BackendDashboardController::class => 'index',
            BackendPostController::class => 'index, sendPostNotifications',
            BackendCommentController::class => 'index, listPending, listByPost',
            BackendSubscriberController::class => 'indexPostSubscriber, indexBlogSubscriber',
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:'.$packageKey.'/Resources/Public/Icons/module.png',
            'labels' => 'LLL:EXT:'.$packageKey.'/Resources/Private/Language/locallang_mod.xlf',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
        ]
    );
}, 't3extblog');
