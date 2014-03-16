<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_t3blog_cat'] = array (
	'ctrl' => $TCA['tx_t3blog_cat']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,parent_id,catname,description'
	),
	'feInterface' => $TCA['tx_t3blog_cat']['feInterface'],
	'columns' => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(0, 0, 0, 12, 31, 2020),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'parent_id' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_cat.parent_id',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_t3blog_cat',
				'foreign_table_where' => ' AND (tx_t3blog_cat.sys_language_uid = 0 OR tx_t3blog_cat.l18n_parent = 0) AND tx_t3blog_cat.pid = ###CURRENT_PID### AND tx_t3blog_cat.uid != ###THIS_UID### ORDER BY tx_t3blog_cat.sorting',
				'renderMode' => 'tree',
				'subType' => 'db',
				'treeConfig' => array(
					'parentField' => 'parent_id',
					'appearance' => array(
						'expandAll' => TRUE,
						'showHeader' => FALSE,
						'maxLevels' => 99,
					),
				),
				'size' => 10,
				'autoSizeMax' => 20,
				'minitems' => 0,
				'maxitems' => 1
			)
		),
		'catname' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_cat.catname',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),

		'description' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_cat.description',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => '--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_cat.generalTab;;;;1-1-1,catname,description,parent_id, --div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_cat.access,--palette--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_cat.access;1')
	),
    'palettes' => array (
        '1' => array('showitem' => 'starttime, endtime, fe_group, hidden','canNotCollapse'=>1)
    )
);

?>