<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Blogsystem',
	array(
		'Blog' => 'list, show',
		'Posts' => 'list, show',
		'Category' => 'list, show',
		'Comment' => 'list, show, new, create, edit, update, delete',
		
	),
	// non-cacheable actions
	array(
		'Blog' => '',
		'Posts' => '',
		'Category' => '',
		'Comment' => 'create, update, delete',
		
	)
);

?>