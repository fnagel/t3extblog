<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Blogsystem',
	'Blogsystem'
);

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'T3Blog Extbase');

$tmp_t3extblog_columns = array(

	'title' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_posts.title',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim,required'
		),
	),
	'author' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_posts.author',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim,required'
		),
	),
	'publish_date' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_posts.publish_date',
		'config' => array(
			'type' => 'input',
			'size' => 10,
			'eval' => 'datetime,required',
			'checkbox' => 1,
			'default' => time()
		),
	),
	'allow_comments' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_posts.allow_comments',
		'config' => array(
			'type' => 'check',
			'default' => 0
		),
	),
	'tag_cloud' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_posts.tag_cloud',
		'config' => array(
			'type' => 'text',
			'cols' => 40,
			'rows' => 15,
			'eval' => 'trim'
		),
	),
	'number_of_views' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_posts.number_of_views',
		'config' => array(
			'type' => 'input',
			'size' => 4,
			'eval' => 'int'
		),
	),
	'content' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_posts.content',
		'config' => array(
			'type' => 'select',
			'foreign_table' => 'tt_content',
			'MM' => 'tx_t3extblog_posts_content_mm',
			'size' => 10,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'multiple' => 0,
			'wizards' => array(
				'_PADDING' => 1,
				'_VERTICAL' => 1,
				'edit' => array(
					'type' => 'popup',
					'title' => 'Edit',
					'script' => 'wizard_edit.php',
					'icon' => 'edit2.gif',
					'popup_onlyOpenIfSelected' => 1,
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				'add' => Array(
					'type' => 'script',
					'title' => 'Create new',
					'icon' => 'add.gif',
					'params' => array(
						'table' => 'tt_content',
						'pid' => '###CURRENT_PID###',
						'setValue' => 'prepend'
						),
					'script' => 'wizard_add.php',
				),
			),
		),
	),
	'category' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_posts.category',
		'config' => array(
			'type' => 'select',
			'foreign_table' => 'tx_t3blog_cat',
			'MM' => 'tx_t3extblog_posts_category_mm',
			'size' => 10,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'multiple' => 0,
			'wizards' => array(
				'_PADDING' => 1,
				'_VERTICAL' => 1,
				'edit' => array(
					'type' => 'popup',
					'title' => 'Edit',
					'script' => 'wizard_edit.php',
					'icon' => 'edit2.gif',
					'popup_onlyOpenIfSelected' => 1,
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
					),
				'add' => Array(
					'type' => 'script',
					'title' => 'Create new',
					'icon' => 'add.gif',
					'params' => array(
						'table' => 'tx_t3blog_cat',
						'pid' => '###CURRENT_PID###',
						'setValue' => 'prepend'
						),
					'script' => 'wizard_add.php',
				),
			),
		),
	),
	'comments' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_posts.comments',
		'config' => array(
			'type' => 'inline',
			'foreign_table' => 'tx_t3blog_com',
			'foreign_field' => 'posts',
			'maxitems'      => 9999,
			'appearance' => array(
				'collapseAll' => 0,
				'levelLinksPosition' => 'top',
				'showSynchronizationLink' => 1,
				'showPossibleLocalizationRecords' => 1,
				'showAllLocalizationLink' => 1
			),
		),
	),
);

t3lib_extMgm::addTCAcolumns('tx_t3blog_post',$tmp_t3extblog_columns);

$TCA['tx_t3blog_post']['columns'][$TCA['tx_t3blog_post']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_post.tx_extbase_type.Tx_T3extblog_Posts','Tx_T3extblog_Posts');

$TCA['tx_t3blog_post']['types']['Tx_T3extblog_Posts']['showitem'] = $TCA['tx_t3blog_post']['types']['1']['showitem'];
$TCA['tx_t3blog_post']['types']['Tx_T3extblog_Posts']['showitem'] .= ',--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_posts,';
$TCA['tx_t3blog_post']['types']['Tx_T3extblog_Posts']['showitem'] .= 'title, author, publish_date, allow_comments, tag_cloud, number_of_views, content, category, comments';

$tmp_t3extblog_columns = array(

	'title_text' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_content.title_text',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim,required'
		),
	),
);

t3lib_extMgm::addTCAcolumns('tt_content',$tmp_t3extblog_columns);

$TCA['tt_content']['columns'][$TCA['tt_content']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tt_content.tx_extbase_type.Tx_T3extblog_Content','Tx_T3extblog_Content');

$TCA['tt_content']['types']['Tx_T3extblog_Content']['showitem'] = $TCA['tt_content']['types']['1']['showitem'];
$TCA['tt_content']['types']['Tx_T3extblog_Content']['showitem'] .= ',--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_content,';
$TCA['tt_content']['types']['Tx_T3extblog_Content']['showitem'] .= 'title_text';

$tmp_t3extblog_columns = array(

	'name' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_category.name',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim,required'
		),
	),
	'description' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_category.description',
		'config' => array(
			'type' => 'text',
			'cols' => 40,
			'rows' => 15,
			'eval' => 'trim',
			'wizards' => array(
				'RTE' => array(
					'icon' => 'wizard_rte2.gif',
					'notNewRecords'=> 1,
					'RTEonly' => 1,
					'script' => 'wizard_rte.php',
					'title' => 'LLL:EXT:cms/locallang_ttc.:bodytext.W.RTE',
					'type' => 'script'
				)
			)
		),
		'defaultExtras' => 'richtext[]',
	),
);

t3lib_extMgm::addTCAcolumns('tx_t3blog_cat',$tmp_t3extblog_columns);

$TCA['tx_t3blog_cat']['columns'][$TCA['tx_t3blog_cat']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_cat.tx_extbase_type.Tx_T3extblog_Category','Tx_T3extblog_Category');

$TCA['tx_t3blog_cat']['types']['Tx_T3extblog_Category']['showitem'] = $TCA['tx_t3blog_cat']['types']['1']['showitem'];
$TCA['tx_t3blog_cat']['types']['Tx_T3extblog_Category']['showitem'] .= ',--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_category,';
$TCA['tx_t3blog_cat']['types']['Tx_T3extblog_Category']['showitem'] .= 'name, description';

$tmp_t3extblog_columns = array(

	'title' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_comment.title',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'author' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_comment.author',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'email' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_comment.email',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim,required'
		),
	),
	'website' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_comment.website',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'date' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_comment.date',
		'config' => array(
			'type' => 'input',
			'size' => 10,
			'eval' => 'datetime',
			'checkbox' => 1,
			'default' => time()
		),
	),
	'text' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_comment.text',
		'config' => array(
			'type' => 'text',
			'cols' => 40,
			'rows' => 15,
			'eval' => 'trim,required',
			'wizards' => array(
				'RTE' => array(
					'icon' => 'wizard_rte2.gif',
					'notNewRecords'=> 1,
					'RTEonly' => 1,
					'script' => 'wizard_rte.php',
					'title' => 'LLL:EXT:cms/locallang_ttc.:bodytext.W.RTE',
					'type' => 'script'
				)
			)
		),
		'defaultExtras' => 'richtext[]',
	),
	'approved' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_comment.approved',
		'config' => array(
			'type' => 'check',
			'default' => 0
		),
	),
	'spam' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_comment.spam',
		'config' => array(
			'type' => 'check',
			'default' => 0
		),
	),
);

$tmp_t3extblog_columns['posts'] = array(
	'config' => array(
		'type' => 'passthrough',
	)
);

t3lib_extMgm::addTCAcolumns('tx_t3blog_com',$tmp_t3extblog_columns);

$TCA['tx_t3blog_com']['columns'][$TCA['tx_t3blog_com']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3blog_com.tx_extbase_type.Tx_T3extblog_Comment','Tx_T3extblog_Comment');

$TCA['tx_t3blog_com']['types']['Tx_T3extblog_Comment']['showitem'] = $TCA['tx_t3blog_com']['types']['1']['showitem'];
$TCA['tx_t3blog_com']['types']['Tx_T3extblog_Comment']['showitem'] .= ',--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xlf:tx_t3extblog_domain_model_comment,';
$TCA['tx_t3blog_com']['types']['Tx_T3extblog_Comment']['showitem'] .= 'title, author, email, website, date, text, approved, spam';

?>