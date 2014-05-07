<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Blogsystem',
	array(
		'Post' => 'list, tag, category, show, permalink, preview',
		'Comment' => 'create',
	),
	// non-cacheable actions
	array(
		'Post' => 'permalink, preview',
		'Comment' => 'create',
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
		'Subscriber' => 'list, delete, error, confirm, logout',
	),
	// non-cacheable actions
	array(
		'Subscriber' => 'list, delete, error, confirm, logout',
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

// add BE hooks
if (TYPO3_MODE == 'BE') {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:t3extblog/Classes/Hooks/Tcemain.php:Tx_T3extblog_Hooks_Tcemain';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:t3extblog/Classes/Hooks/Tcemain.php:Tx_T3extblog_Hooks_Tcemain';
}

// add RealURL configuration
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['t3extblog'] = 'EXT:t3extblog/Classes/Hooks/RealUrl.php:Tx_T3extblog_Hooks_RealUrl->extensionConfiguration';

// support for dd_googlesitemap
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['dd_googlesitemap']['sitemap']['t3extblog'] = 'Tx_T3extblog_Hooks_Sitemap_Generator->main';

// add cHash configuration
// See: http://forum.typo3.org/index.php?t=msg&th=203350
$requiredParameters = array(
	'tx_t3extblog_blogsystem[action]',
	'tx_t3extblog_blogsystem[controller]',
	'tx_t3extblog_blogsystem[post]',
	'tx_t3extblog_blogsystem[permalinkPost]',
	'tx_t3extblog_blogsystem[previewPost]',
	'tx_t3extblog_blogsystem[tag]',
	'tx_t3extblog_blogsystem[category]',
	'tx_t3extblog_blogsystem[@widget_0][currentPage]',
);
$GLOBALS['TYPO3_CONF_VARS']['FE']['cHashRequiredParameters'] .= ',' . implode(',', $requiredParameters);

?>