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

$contentTypeName = TcaUtility::registerPlugin('LatestComments', 'latestcomments');
TcaUtility::addFlexForm(
    $contentTypeName,
    '/Configuration/FlexForms/LatestComments.xml'
);

$contentTypeName = TcaUtility::registerPlugin('LatestPosts', 'latestposts');
TcaUtility::addFlexForm(
    $contentTypeName,
    '/Configuration/FlexForms/LatestPosts.xml'
);
