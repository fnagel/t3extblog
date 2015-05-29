<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_t3blog_com_nl'] = array(
	'ctrl' => $TCA['tx_t3blog_com_nl']['ctrl'],
	'columns' => array(
		'email' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.email',
			'config' => array(
				'type' => 'input',
				'eval' => 'required'
			)
		),
		'name' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.name',
			'config' => array(
				'type' => 'input',
				'eval' => 'required'
			)
		),
		'post_uid' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.post_uid',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'db',
				'prepend_tname' => false,
				'allowed' => 'tx_t3blog_post',
				'minitems' => 1,
				'maxitems' => 1,
				'size' => 1
			)
		),
		'lastsent' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.lastsent',
			'config' => array(
				'type' => 'input',
				'eval' => 'datetime'
			)
		),
		'code' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.code',
			'config' => array(
				'type' => 'input',
			)
		)
	),
	'types' => array(
		'0' => array(
			'showitem' => 'name,email,--div--,post_uid,lastsent,code')
	),
);

?>