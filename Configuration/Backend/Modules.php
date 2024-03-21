<?php

use FelixNagel\T3extblog\Controller\BackendDashboardController;
use FelixNagel\T3extblog\Controller\BackendPostController;
use FelixNagel\T3extblog\Controller\BackendCommentController;
use FelixNagel\T3extblog\Controller\BackendSubscriberController;

defined('TYPO3') || die();

// Register Backend Module
return [
    'web_T3extblogBlogsystem' => [
        'parent' => 'web',
        'position' => ['after' => 'web_info'],
        'access' => 'user,group',
        'workspaces' => '*',
        'iconIdentifier' => 'extensions-t3extblog-plugin',
        'labels' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'T3extblog',
        'controllerActions' => [
            BackendDashboardController::class => [
                'index',
            ],
            BackendPostController::class => [
                'index',
                'sendPostNotifications',
            ],
            BackendCommentController::class => [
                'index',
                'listPending',
                'listByPost',
            ],
            BackendSubscriberController::class => [
                'indexPostSubscriber',
                'indexBlogSubscriber',
            ],
        ],
    ],
];
