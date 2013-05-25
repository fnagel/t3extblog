<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'T3Extblog');


Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Blogsystem',
	'T3Blog Extbase: Blogsystem'
);

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'SubscriptionManager',
	'T3Blog Extbase: Subscription Manager'
);

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Archive',
	'T3Blog Extbase: Archive'
);

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Categories',
	'T3Blog Extbase: Categories'
);

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'LatestPosts',
	'T3Blog Extbase: LatestPosts'
);

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'LatestComments',
	'T3Blog Extbase: LatestComments'
);

?>