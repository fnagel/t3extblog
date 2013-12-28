<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Blogsystem',
	array(
		'Post' => 'show, permalink, list',
		'Comment' => 'create',		
	),
	// non-cacheable actions
	array(
		'Post' => '',
		'Comment' => 'create, update',
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Archive',
	array(
		'Post' => 'archive',	
	),
	// non-cacheable actions
	array(
		'Post' => '',
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Rss',
	array(
		'Post' => 'rss',
	),
	// non-cacheable actions
	array(
		'Post' => '',
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'SubscriptionManager',
	array(
		'Subscriber' => 'list, delete, error, confirm',		
	),
	// non-cacheable actions
	array(
		'Subscriber' => 'list, delete, error, confirm',		
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Categories',
	array(
		'Category' => 'list, show',
	),
	// non-cacheable actions
	array(
		'Category' => '',
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'LatestPosts',
	array(
		'Post' => 'latest',	
	),
	// non-cacheable actions
	array(
		'Post' => '',	
	)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'LatestComments',
	array(
		'Comment' => 'latest',		
	),
	// non-cacheable actions
	array(
		'Comment' => '',		
	)
);


// add RealURL autoconfiguration
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['t3extblog'] =
	'EXT:t3extblog/Classes/Hooks/RealUrlAutoConfig.php:Tx_T3extblog_Hooks_RealUrlAutoConfig->addConfig';


?>