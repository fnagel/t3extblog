<?php

return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'author',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xlf:LGL.prependAtCopy',
		'hideAtCopy' => TRUE,
		'default_sortby' => 'ORDER BY date DESC',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'typeicon_classes' => [
			'default' => 'extensions-t3extblog-post',
		],
		'dividers2tabs' => TRUE,
		'searchFields' => 'title',
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,title,
			author,be_user,date,content,allow_comments,cat,tagClouds,trackback,number_views,mails_sent'
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
		'l18n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_t3blog_post',
				'foreign_table_where' => 'AND tx_t3blog_post.pid=###CURRENT_PID### AND tx_t3blog_post.sys_language_uid IN (-1,0)',
				'showIconTable' => FALSE,
			)
		),
		'l18n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough'
			)
		),
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
				'renderType' => 'selectMultipleSideBySide',
				'size' => 7,
				'maxitems' => 20,
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.usergroups', '--div--')
				),
				'exclusiveKeys' => '-1,-2',
				'foreign_table' => 'fe_groups',
				'foreign_table_where' => 'ORDER BY fe_groups.title',
				'showIconTable' => FALSE,
			)
		),
		'title' => array(
			'exclude' => 1,
			'l10n_mode' => 'prefixLangTitle',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.title',
			'config' => array(
				'type' => 'input',
				'eval' => 'required',
			)
		),
		'tagClouds' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tagClouds',
			'config' => array(
				'placeholder' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tagClouds.placeholder',
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
				'renderType' => 'selectSingle',
				'foreign_table' => 'be_users',
				'foreign_table_where' => ' AND be_users.disable = 0 ' .
					'AND (be_users.username != "_cli_lowlevel" AND be_users.username != "_cli_scheduler") ' .
					'ORDER BY be_users.username',
				'showIconTable' => FALSE,
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
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.content',
			'config' => array(
				'type' => 'inline',
				'allowed' => 'tt_content',
				'foreign_table' => 'tt_content',
				'foreign_table_field' => 'irre_parenttable',
				'foreign_field' => 'irre_parentid',
				'minitems' => 0,
				'maxitems' => 99,
				'appearance' => array(
					'collapseAll' => 1,
					'expandSingle' => 1,
					'levelLinksPosition' => 'bottom',
					'useSortable' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showRemovedLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1,
					'showSynchronizationLink' => 1,
					'enabledControls' => array(
						'info' => FALSE,
					)
				),
				'behaviour' => array(
					'enableCascadingDelete' => TRUE
				),
				'foreign_selector_fieldTcaOverride' => array(
					'l10n_mode' => 'prefixLangTitle'
				)
			)
		),
		'allow_comments' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
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
			'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.cat',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectTree',
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
			'l10n_mode' => 'noCopy',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.trackback',
			'config' => array(
				'type' => 'text',
				'cols' => '45',
				'rows' => '3',
				'wrap' => 'off',
				'softref' => 'url',
			)
		),
		'trackback_hash' => array(
			'exclude' => 0,
			'l10n_mode' => 'noCopy',
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
			'l10n_mode' => 'prefixLangTitle',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.number_views',
			'config' => array(
				'type' => 'input',
				'size' => '8',
				'max' => '15',
				'eval' => 'int',
			)
		),
		'meta_description' => array(
			'exclude' => 1,
			'l10n_mode' => 'noCopy',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.meta_description',
			'config' => array(
				'type' => 'text',
				'cols' => '45',
				'rows' => '3',
			)
		),
		'meta_keywords' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.meta_keywords',
			'config' => array(
				'placeholder' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.meta_keywords.placeholder',
				'type' => 'text',
				'cols' => '45',
				'rows' => '2',
			)
		),
		'preview_mode' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.preview_mode',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'default' => 0,
				'items' => array(
					array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.preview_mode.0', '0'),
					array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.preview_mode.1', '1'),
					array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.preview_mode.2', '2'),
					array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.preview_mode.3', '3'),
				),
			)
		),
		'preview_text' => array(
			'exclude' => 1,
			'l10n_mode' => 'noCopy',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.preview_text',
			'config' => array(
				'type' => 'text',
				'cols' => '45',
				'rows' => '10',
				'softref' => 'typolink_tag,email[subst],url',
			),
			'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
		),
		'preview_image' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.preview_image',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
				'preview_image',
				array(
					'maxitems' => 1,
					'foreign_types' => array(
						\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
							'showitem' => '
		                    --palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;t3extblogPostPreviewImagePalette,
		                    --palette--;;filePalette'
						),
					),
					'appearance' => array(
						'collapseAll' => 1,
						'showPossibleLocalizationRecords' => 1,
						'showRemovedLocalizationRecords' => 1,
						'showAllLocalizationLink' => 1,
						'showSynchronizationLink' => 1,
					),
				),
				$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
			)
		),
		'mails_sent' => array(
			'displayCond' => 'HIDE_FOR_NON_ADMINS',
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.mails_sent',
			'config' => array(
				'type' => 'check',
			)
		),
	),
	'types' => array(
		'0' => array(
			'showitem' => '
				--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tabs.post;;;1-1-1,
					sys_language_uid,l18n_parent,l18n_diffsource,date,author;;;;2-2-2,be_user, title;;;;3-3-3,content,
				--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tabs.preview,
					preview_mode, preview_image,preview_text,
				--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tabs.category,
					tagClouds,cat,
				--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tabs.interactive,
					allow_comments,trackback,number_views,mails_sent,
				--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tabs.meta,
					meta_description,meta_keywords,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility;visibility,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.access;access'
		)
	),
	'palettes' => array(
		'visibility' => array(
			'showitem' => 'hidden',
		),
		'access' => array(
			'showitem' => '
				starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.starttime_formlabel,
				endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.endtime_formlabel,
				--linebreak--, fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.fe_group_formlabel'
		),
	),
);
