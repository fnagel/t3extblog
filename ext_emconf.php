<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "t3extblog".
 *
 * Auto generated 18-02-2014 01:36
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'T3Blog Extbase',
	'description' => 'A flexible blog extension powered by Extbase / Fluid which aims to replace t3blog.',
	'category' => 'plugin',
	'author' => 'Felix Nagel',
	'author_email' => 'info@felixnagel.com',
	'author_company' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '2.1.0-dev',
	'constraints' => array(
		'depends' => array(
			'php' => '5.5.0-7.999.999',
			'typo3' => '6.2.0-7.6.99',
			'extbase' => '',
			'fluid' => '',
		),
		'conflicts' => array(
			't3blog' => '',
		),
		'suggests' => array(
			'dd_googlesitemap' => '2.0.0-2.0.99',
		),
	),
);
