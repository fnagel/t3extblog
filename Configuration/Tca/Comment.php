<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_t3blog_com'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_t3blog_com']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,author,email,website,date,text,approved,spam,fk_post'
	),
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'starttime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'datetime',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'datetime',
				'checkbox' => '0',
				'default' => '0',
				'range' => array(
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m') - 1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectMultipleSideBySide',
				'items' => array(
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups',
				'showIconTable' => FALSE,
			)
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.title',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			)
		),
		'author' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.author',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'email' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.email',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'website' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.website',
			'config' => array(
				'type' => 'input',
				'size' => '30',
			)
		),
		'date' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.date',
			'config' => array(
				'type' => 'input',
				'size' => '12',
				'max' => '20',
				'eval' => 'datetime',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'text' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.text',
			'config' => array(
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext.W.RTE',
						'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_rte.gif',
						'module' => array(
							'name' => 'wizard_rte'
						)
					),
				),
			)
		),
		'approved' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.approved',
			'config' => array(
				'type' => 'check',
			)
		),
		'spam' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.spam',
			'config' => array(
				'type' => 'check',
			)
		),
		'fk_post' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.fk_post',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_t3blog_post',
				'foreign_table_where' => ' AND tx_t3blog_post.deleted = 0 AND tx_t3blog_post.pid=###CURRENT_PID###',
				'showIconTable' => FALSE,
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
		'mails_sent' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.mails_sent',
			'config' => array(
				'type' => 'check',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => '
			--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.tabs.general,
				fk_post, title, author, email, website, date, text,
			--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.tabs.access,
				--palette--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.tabs.approval;1,
				--palette--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.tabs.access;2')
	),
	'palettes' => array(
		'1' => array('showitem' => 'approved, spam, hidden', 'canNotCollapse' => 1),
		'2' => array('showitem' => 'starttime, endtime, fe_group')
	)
);

// @todo Remove this when 6.2 is no longer relevant
if (version_compare(TYPO3_branch, '7.0', '<')) {
	$GLOBALS['TCA']['tx_t3blog_com']['columns']['text']['config']['wizards']['RTE']['title'] =
		'LLL:EXT:cms/locallang_ttc.xml:bodytext.W.RTE';
	$GLOBALS['TCA']['tx_t3blog_com']['columns']['text']['config']['wizards']['RTE']['icon'] = 'wizard_rte2.gif';
}
if (version_compare(TYPO3_branch, '7.0', '>')) {
	$GLOBALS['TCA']['tx_t3blog_com']['columns']['email']['config']['eval'] .= ',email';
}