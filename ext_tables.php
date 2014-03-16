<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

include(t3lib_extMgm::extPath($_EXTKEY).'Configuration/Tca/T3blog.php');

$extensionName = t3lib_div::underscoredToUpperCamelCase($_EXTKEY);

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
$pluginSignature = strtolower($extensionName) . '_latestposts';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform,recursive';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/LatestPosts.xml');

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'LatestComments',
	'T3Blog Extbase: LatestComments'
);


if (TYPO3_MODE === 'BE') {
	/**
	* Registers a Backend Module
	*/
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'web',	// Make module a submodule of 'web'
		'Tx_T3extblog',	// Submodule key
		'', // Position
		array(
			// An array holding the controller-action-combinations that are accessible
			'BackendPost'		=> 'index',
			'BackendComment'	=> 'index, list'
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml',
			'navigationComponentId' => 'typo3-pagetree',
		)
	);
}

?>