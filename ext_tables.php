<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

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

?>