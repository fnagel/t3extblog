<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Blogsystem',
	array(
		'Post' => 'list, show',
		'Category' => 'list, show',
		'Comment' => 'list, show, new, create, edit, update, delete',		
	),
	// non-cacheable actions
	array(
		'Post' => '',
		'Category' => '',
		'Comment' => 'create, update, delete',		
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

?>