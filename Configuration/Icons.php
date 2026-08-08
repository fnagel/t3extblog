<?php

use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'extensions-t3extblog-post' => [
        'provider' => BitmapIconProvider::class,
        'source' => 'EXT:t3extblog/Resources/Public/Icons/page.png',
    ],
    'extensions-t3extblog-category' => [
        'provider' => BitmapIconProvider::class,
        'source' => 'EXT:t3extblog/Resources/Public/Icons/category.png',
    ],
    'extensions-t3extblog-comment' => [
        'provider' => BitmapIconProvider::class,
        'source' => 'EXT:t3extblog/Resources/Public/Icons/comment.png',
    ],
    'extensions-t3extblog-subscriber' => [
        'provider' => BitmapIconProvider::class,
        'source' => 'EXT:t3extblog/Resources/Public/Icons/subscriber.png',
    ],
    'extensions-t3extblog-trackback' => [
        'provider' => BitmapIconProvider::class,
        'source' => 'EXT:t3extblog/Resources/Public/Icons/trackback.png',
    ],
    'extensions-t3extblog-plugin' => [
        'provider' => BitmapIconProvider::class,
        'source' => 'EXT:t3extblog/Resources/Public/Icons/plugin.png',
    ],
    'extensions-t3extblog-module' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:t3extblog/Resources/Public/Icons/module.svg',
    ],
    'tcarecords-pages-contains-t3blog' => [
        'provider' => BitmapIconProvider::class,
        'source' => 'EXT:t3extblog/Resources/Public/Icons/folder.png',
    ],
];
