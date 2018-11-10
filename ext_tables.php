<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function ($packageKey) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_post');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_cat');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_com');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_com_nl');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_blog_nl');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_pingback');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_trackback');

    if (TYPO3_MODE === 'BE') {
        // Add icons to registry
        /* @var $iconRegistry \TYPO3\CMS\Core\Imaging\IconRegistry */
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'extensions-t3extblog-post',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/page.png']
        );
        $iconRegistry->registerIcon(
            'extensions-t3extblog-category',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/category.png']
        );
        $iconRegistry->registerIcon(
            'extensions-t3extblog-comment',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/comment.png']
        );
        $iconRegistry->registerIcon(
            'extensions-t3extblog-subscriber',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/subscriber.png']
        );
        $iconRegistry->registerIcon(
            'extensions-t3extblog-trackback',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/trackback.png']
        );
        $iconRegistry->registerIcon(
            'extensions-t3extblog-plugin',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/module.png']
        );
        $iconRegistry->registerIcon(
            'tcarecords-pages-contains-t3blog',
            \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
            ['source' => 'EXT:t3extblog/Resources/Public/Icons/folder.png']
        );

        // Register  Backend Module
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'FelixNagel.'.$packageKey,
            'web',
            'Tx_T3extblog',
            '',
            [
                'BackendDashboard' => 'index',
                'BackendPost' => 'index, sendPostNotifications',
                'BackendComment' => 'index, listPending, listByPost',
                'BackendSubscriber' => 'indexPostSubscriber, indexBlogSubscriber',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:'.$packageKey.'/Resources/Public/Icons/module.png',
                'labels' => 'LLL:EXT:'.$packageKey.'/Resources/Private/Language/locallang_mod.xml',
                'navigationComponentId' => 'typo3-pagetree',
            ]
        );
    }
}, $_EXTKEY);
