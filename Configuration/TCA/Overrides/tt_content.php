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

TcaUtility::registerPlugin('LatestComments', 'latestcomments');
TcaUtility::addFlexForm(
    'LatestComments',
    '/Configuration/FlexForms/LatestComments.xml'
);

TcaUtility::registerPlugin('LatestPosts', 'latestposts');
TcaUtility::addFlexForm(
    'LatestPosts',
    '/Configuration/FlexForms/LatestPosts.xml'
);
