<?php

return [
    'blog' => [
        'title' => \FelixNagel\T3extblog\Dashboard\Provider\AbstractDataProvider::LOCALLANG_FILE . 'dashboardPreset.title',
        'description' => \FelixNagel\T3extblog\Dashboard\Provider\AbstractDataProvider::LOCALLANG_FILE . 'dashboardPreset.description',
        'iconIdentifier' => 'extensions-t3extblog-plugin',
        'defaultWidgets' => [
            't3extblogPendingCommentsNumberWithIconWidget',
            't3extblogDraftPosts',
            't3extblogLatestComments',
            't3extblogStatisticChart',
            't3extblogPostSubscriber',
            't3extblogBlogSubscriber',
            't3extblogLatestPosts',
        ],
        'showInWizard' => true,
    ],
];
