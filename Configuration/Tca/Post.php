<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_t3blog_post'] = array(
	'ctrl' => $TCA['tx_t3blog_post']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,author,be_user,date,content,allow_comments,cat,tagClouds,trackback,number_views'
	),
	'feInterface' => $TCA['tx_t3blog_post']['feInterface'],
	'columns' => array(
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => array(
				'type' => 'check',
				'default' => '1'
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
				'items' => array(
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.title',
			'config' => array(
				'type' => 'input',
				'eval' => 'required',
			)
		),
		'tagClouds' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tagClouds',
			'config' => array(
				'type' => 'input',
				'size' => '150',
				'max' => '200',
				'eval' => 'trim, lower',
			)
		),
		'author' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.author',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'be_users',
				'foreign_table_where' => ' and be_users.disable = 0 ORDER BY be_users.username',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'prepend_tname' => FALSE
			)
		),
		'date' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.date',
			'config' => array(
				'type' => 'input',
				'size' => '12',
				'max' => '20',
				'eval' => 'datetime',
				'checkbox' => '0',
				'default' => mktime(date("H"), date("i"), 0, date("m"), date("d"), date("Y"))
			)
		),
		'content' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.content',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tt_content',
				'foreign_field' => 'irre_parentid',
				'foreign_table_field' => 'irre_parenttable',
				'maxitems' => 100,
				'appearance' => array(
					'showSynchronizationLink' => 0,
					'showAllLocalizationLink' => 0,
					'showPossibleLocalizationRecords' => 0,
					'showRemovedLocalizationRecords' => 0,
					'expandSingle' => 1
				),
				'behaviour' => array(),
			)

		),
		'allow_comments' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.allow_comments',
			'config' => array(
				'type' => 'radio',
				'default' => 0,
				'items' => array(
					array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.allow_comments.I.0', '0'),
					array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.allow_comments.I.1', '1'),
					array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.allow_comments.I.2', '2'),
				),
			)
		),
		'cat' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.cat',
			'config' => array(
				'type' => 'select',
				'renderMode' => 'tree',
				'treeConfig' => array(
					'parentField' => 'parent_id',
					'appearance' => array(
						'expandAll' => TRUE,
						'showHeader' => TRUE,
					),
				),
				'MM' => 'tx_t3blog_post_cat_mm',
				'foreign_table' => 'tx_t3blog_cat',
				'foreign_table_where' => ' AND tx_t3blog_cat.pid = ###CURRENT_PID### AND tx_t3blog_cat.hidden = 0 AND tx_t3blog_cat.deleted = 0 AND (tx_t3blog_cat.sys_language_uid = 0 OR tx_t3blog_cat.l18n_parent = 0) ORDER BY tx_t3blog_cat.sorting',
				'size' => 10,
				'autoSizeMax' => 20,
				'minitems' => 1,
				'maxitems' => 20,
			)
		),
		'trackback' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.trackback',
			'config' => array(
				'type' => 'text',
				'cols' => '45',
				'rows' => '3',
				'wrap' => 'off',
			)
		),
		'trackback_hash' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.trackback_hash',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '130',
				'eval' => 'trim',
			)
		),
		'number_views' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.number_views',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '15',
				'eval' => 'int',
			)
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => '
				--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tabs.post;;;1-1-1,
					date,author;;;;2-2-2,be_user, title;;;;3-3-3,content,
				--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tabs.category,
					tagClouds,cat,
				--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tabs.interactive,
					allow_comments,trackback,number_views,
				--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tabs.access,
					--palette--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tabs.access;1;2-2-2,
			')
	),
	'palettes' => array(
		'1' => array('showitem' => 'starttime, endtime, fe_group, hidden', 'canNotCollapse' => 1)
	)
);

?>