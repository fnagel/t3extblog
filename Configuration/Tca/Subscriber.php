<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_t3blog_com_nl'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_t3blog_com_nl']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,email,name,lastsent,post_uid,code'
	),
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			)
		),
		'email' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.email',
			'config' => array(
				'type' => 'input',
				'eval' => 'required',
			)
		),
		'name' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.name',
			'config' => array(
				'type' => 'input',
				'eval' => 'required',
			)
		),
		'post_uid' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.post_uid',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_t3blog_post',
				'foreign_table_where' => ' AND tx_t3blog_post.deleted = 0 AND tx_t3blog_post.pid=###CURRENT_PID###',
				'minitems' => 1,
				'maxitems' => 1,
				'size' => 1,
				'wizards' => array(
					'add' => Array(
						'type' => 'suggest',
					),
				),
			)
		),
		'lastsent' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.lastsent',
			'config' => array(
				'type' => 'input',
				'eval' => 'datetime,required',
				'size' => '12',
			)
		),
		'code' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.code',
			'config' => array(
				'type' => 'input',
				'readOnly' => TRUE,
				'size' => '30',
			)
		)
	),
	'types' => array(
		'0' => array('showitem' => '
			name, email,
			--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.tabs.meta,
				post_uid, lastsent, code')
	),
);
