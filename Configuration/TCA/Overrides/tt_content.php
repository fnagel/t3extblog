<?php

use FelixNagel\T3extblog\Utility\TcaUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') || die();

// Add group in new element wizard
ExtensionManagementUtility::addTcaSelectItemGroup(
    'tt_content',
    'CType',
    'blog',
    'LLL:EXT:t3extblog/Resources/Private/Language/locallang_plugins.xlf:tab.title',
);

TcaUtility::registerPlugin('Blogsystem', 'blogsystem');

TcaUtility::registerPlugin('SubscriptionManager', 'subscriptionmanager');

TcaUtility::registerPlugin('BlogSubscription', 'blogsubscription');

TcaUtility::registerPlugin('Archive', 'archive');

TcaUtility::registerPlugin('Rss', 'rss');

TcaUtility::registerPlugin('Categories', 'categories');

TcaUtility::registerPlugin('Tags', 'tags');

TcaUtility::registerPlugin('LatestComments', 'latestcomments', '/Configuration/FlexForms/LatestComments.xml');

TcaUtility::registerPlugin('LatestPosts', 'latestposts', '/Configuration/FlexForms/LatestPosts.xml');

TcaUtility::registerPlugin('RelatedPosts', 'relatedposts');

// Add columns
$additionalColumns = [
    'irre_parenttable' => [
        'label' => 'Blog-Post',
        'config' => [
            'type' => 'passthrough',
        ],
    ],
    'irre_parentid' => [
        'label' => 'Blog-Post',
        'config' => [
            'type' => 'passthrough',
        ],
    ],
];

ExtensionManagementUtility::addTCAcolumns(
    'tt_content',
    $additionalColumns
);

ExtensionManagementUtility::addFieldsToPalette(
    'tt_content',
    'general',
    'irre_parenttable, irre_parentid'
);
