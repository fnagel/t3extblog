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
		'Subscriber' => 'list, new, create, edit, update, delete',
		
	),
	// non-cacheable actions
	array(
		'Post' => '',
		'Category' => '',
		'Comment' => 'create, update, delete',
		'Subscriber' => 'create, update, delete',
		
	)
);

?>