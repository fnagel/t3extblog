<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Add static TS
t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'T3Extblog: Default setup (needed)');
t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript/Rss', 'T3Extblog: Rss setup');


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
	'Rss',
	'T3Blog Extbase: RSS'
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


if (TYPO3_MODE === 'BE') {
	// add modules to t3blog one
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'txt3blogM1',
		'Tx_T3extblog_Post',
		'',
		array(
			'BackendPost' => 'index',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			// 'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xlf',
			'labels' => 'T3Extblog: Posts',
		)
	);
}

?>