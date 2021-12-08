<?php

use FelixNagel\T3extblog\Utility\TcaUtility;

defined('TYPO3') || die();

TcaUtility::registerPlugin('Blogsystem', 'blogsystem.title');

TcaUtility::registerPlugin('SubscriptionManager', 'subscriptionmanager.title');

TcaUtility::registerPlugin('BlogSubscription', 'blogsubscription.title');

TcaUtility::registerPlugin('Archive', 'archive.title');

TcaUtility::registerPlugin('Rss', 'rss.title');

TcaUtility::registerPlugin('Categories', 'categories.title');

TcaUtility::registerPlugin('LatestComments', 'latestcomments.title');
TcaUtility::addFlexForm(
    'LatestComments',
    '/Configuration/FlexForms/LatestComments.xml'
);

TcaUtility::registerPlugin('LatestPosts', 'latestposts.title');
TcaUtility::addFlexForm(
    'LatestPosts',
    '/Configuration/FlexForms/LatestPosts.xml'
);
