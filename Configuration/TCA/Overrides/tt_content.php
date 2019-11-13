<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\FelixNagel\T3extblog\Utility\TcaUtility::registerPlugin('Blogsystem', 'blogsystem.title');

\FelixNagel\T3extblog\Utility\TcaUtility::registerPlugin('SubscriptionManager', 'subscriptionmanager.title');

\FelixNagel\T3extblog\Utility\TcaUtility::registerPlugin('BlogSubscription', 'blogsubscription.title');

\FelixNagel\T3extblog\Utility\TcaUtility::registerPlugin('Archive', 'archive.title');

\FelixNagel\T3extblog\Utility\TcaUtility::registerPlugin('Rss', 'rss.title');

\FelixNagel\T3extblog\Utility\TcaUtility::registerPlugin('Categories', 'categories.title');

\FelixNagel\T3extblog\Utility\TcaUtility::registerPlugin('LatestComments', 'latestcomments.title');

\FelixNagel\T3extblog\Utility\TcaUtility::registerPlugin('LatestPosts', 'latestposts.title');
\FelixNagel\T3extblog\Utility\TcaUtility::addFlexForm(
    'LatestPosts',
    '/Configuration/FlexForms/LatestPosts.xml'
);
