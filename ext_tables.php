<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;

defined('TYPO3') || die();

call_user_func(static function ($packageKey) {
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
        ['source' => 'EXT:'.$packageKey.'/Resources/Public/Icons/folder.png']
    );
}, 't3extblog');
