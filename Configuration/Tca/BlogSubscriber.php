<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_t3blog_blog_nl'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_t3blog_blog_nl']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,hidden,email,lastsent,code'
	),
	'columns' => array(
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:cms/locallang_ttc.xlf:sys_language_uid_formlabel',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'special' => 'languages',
				'items' => array(
					array(
						'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
						-1,
						'flags-multiple'
					),
				),
				'default' => 0,
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0',
			)
		),
		'email' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.email',
			'config' => array(
				'type' => 'input',
				'eval' => 'required',
			)
		),
		'lastsent' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.lastsent',
			'config' => array(
				'type' => 'input',
				'eval' => 'datetime,required',
				'size' => '12',
			)
		),
		'code' => array(
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.code',
			'config' => array(
				'type' => 'input',
				'readOnly' => TRUE,
				'size' => '30',
			)
		)
	),
	'types' => array(
		'0' => array('showitem' => '
			sys_language_uid, email, hidden,
			--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blog_nl.tabs.meta,
				lastsent, code')
	),
);
