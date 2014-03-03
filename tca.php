<?php

if (!defined ('TYPO3_MODE')) die ('Access denied.');

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
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl.namel',
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
	'types' => array (
		'0' => array(
			'showitem' => 'name,email,--div--,post_uid,lastsent,code')
	),
);

$TCA['tx_t3blog_post'] = array (
	'ctrl' => $TCA['tx_t3blog_post']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,author,be_user,date,content,allow_comments,cat,tagClouds,trackback,number_views'
	),
	'feInterface' => $TCA['tx_t3blog_post']['feInterface'],
	'columns' => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '1'
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
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.title',
			'config' => Array (
				'type' => 'input',
				'eval' => 'required',
			)
		),
		'tagClouds' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.tagClouds',
			'config' => Array (
	            'type' => 'input',
	            'size' => '150',
	            'max' => '200',
	            'eval' => 'trim, lower',
			)
		),
		'author' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.author',
			'config'  => array (
				'type' => 'select',
				'foreign_table' => 'be_users',
				'foreign_table_where' => 'ORDER BY be_users.username',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'prepend_tname' => FALSE
			)
		),
		'date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.date',
			'config' => Array (
				'type'     => 'input',
				'size'     => '12',
				'max'      => '20',
				'eval'     => 'datetime',
				'checkbox' => '0',
				'default'  => mktime(date("H"),date("i"),0,date("m"),date("d"),date("Y"))
			)
		),
		'content' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.content',
			'config' => array (
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
				'behaviour' => array(
				),
			)

		),
		'allow_comments' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.allow_comments',
			'config' => Array (
				'type' => 'radio',
				'default' => 0,
				'items' => Array (
					Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.allow_comments.I.0', '0'),
					Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.allow_comments.I.1', '1'),
					Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.allow_comments.I.2', '2'),
				),
			)
		),
		'cat' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.cat',
			'config' => Array (
				'type' => 'select',
				'form_type' => 'user',
				'userFunc' => 'tx_t3blog_treeview->displayCatTree',
				'treeView' => 1,
				'foreign_table' => 'tx_t3blog_cat',
				'foreign_table_where' => 'AND tx_t3blog_cat.pid = ###CURRENT_PID###  AND tx_t3blog_cat.hidden = 0 AND tx_t3blog_cat.deleted = 0',
				'MM' => 'tx_t3blog_post_cat_mm',
				'size' => 15,
				'minitems' => 1,
				'maxitems' => 20,
			)
		),
		 'trackback' => Array (
	        'exclude' => 1,
	        'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.trackback',
	        'config' => Array (
	            'type' => 'text',
	            'cols' => '45',
	            'rows' => '3',
	            'wrap'=> 'off',
	        )
	    ),
	    'trackback_hash' => Array (
	        'exclude' => 0,
	        'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.trackback_hash',
	        'config' => Array (
	            'type' => 'input',
	            'size' => '30',
	            'max' => '130',
	            'eval' => 'trim',
	        )
	    ),
	    'number_views' => Array (
	        'exclude' => 1,
	        'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.number_views',
	        'config' => Array (
	            'type' => 'input',
	            'size' => '8',
	            'max' => '15',
	            'eval' => 'int',
	        )
	    ),
	),
	'types' => array (
		'0' => array(
			'showitem' => '--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.yourposttab;;;1-1-1,date,author;;;;2-2-2,be_user, title;;;;3-3-3,content,--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.categorize,tagClouds,cat,--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.interactive,allow_comments,trackback,number_views,--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.access,--palette--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post.access;1;2-2-2,')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime, fe_group, hidden','canNotCollapse'=>1)
	)
);



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
			'config' => Array (
				'type' => 'select',
				'form_type' => 'user',
				'userFunc' => 'tx_t3blog_treeview->displayCatTree',
				'treeView' => 1,
				'foreign_table' => 'tx_t3blog_cat',
				'foreign_table_where' => 'AND tx_t3blog_cat.pid = ###CURRENT_PID###',
				'size' => 5,
				'minitems' => 0,
				'maxitems' => 1,
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


$TCA['tx_t3blog_com'] = array (
	'ctrl' => $TCA['tx_t3blog_com']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,author,be_user,email,website,date,text,approved,spam,fk_post'
	),
	'feInterface' => $TCA['tx_t3blog_com']['feInterface'],
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
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'author' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.author',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'email' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.email',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'website' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.website',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
			)
		),
		'date' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.date',
			'config' => Array (
				'type'     => 'input',
				'size'     => '12',
				'max'      => '20',
				'eval'     => 'datetime',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'text' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.text',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => Array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly' => 1,
						'type' => 'script',
						'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon' => 'wizard_rte2.gif',
						'script' => 'wizard_rte.php',
					),
				),
			)
		),
		'approved' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.approved',
			'config' => Array (
				'type' => 'check',
			)
		),
		'spam' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.spam',
			'config' => Array (
				'type' => 'check',
			)
		),
		'fk_post' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com.fk_post',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_t3blog_post',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2;;;;3-3-3 , author;;;;3-3-3, be_user, email, website, date, text;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts], approved, spam, fk_post')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime, fe_group')
	)
);

// $TCA['tx_t3blog_blogroll'] = array (
    // 'ctrl' => $TCA['tx_t3blog_blogroll']['ctrl'],
    // 'interface' => array (
        // 'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,url,image,description'
    // ),
    // 'feInterface' => $TCA['tx_t3blog_blogroll']['feInterface'],
    // 'columns' => array (
        // 'hidden' => array (
            // 'exclude' => 1,
            // 'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            // 'config'  => array (
                // 'type'    => 'check',
                // 'default' => '0'
            // )
        // ),
        // 'starttime' => array (
            // 'exclude' => 1,
            // 'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
            // 'config'  => array (
                // 'type'     => 'input',
                // 'size'     => '8',
                // 'max'      => '20',
                // 'eval'     => 'date',
                // 'default'  => '0',
                // 'checkbox' => '0'
            // )
        // ),
        // 'endtime' => array (
            // 'exclude' => 1,
            // 'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
            // 'config'  => array (
                // 'type'     => 'input',
                // 'size'     => '8',
                // 'max'      => '20',
                // 'eval'     => 'date',
                // 'checkbox' => '0',
                // 'default'  => '0',
                // 'range'    => array (
                    // 'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    // 'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
                // )
            // )
        // ),
        // 'fe_group' => array (
            // 'exclude' => 1,
            // 'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
            // 'config'  => array (
                // 'type'  => 'select',
                // 'items' => array (
                    // array('', 0),
                    // array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
                    // array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
                    // array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
                // ),
                // 'foreign_table' => 'fe_groups'
            // )
        // ),
        // 'title' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.title',
            // 'config' => Array (
                // 'type' => 'input',
                // 'size' => '30',
                // 'eval' => 'required',
            // )
        // ),
        // 'url' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.url',
            // "config" => Array (
                // "type"     => "input",
                // "size"     => "15",
                // "max"      => "255",
                // "checkbox" => "",
                // "eval"     => "trim, required",
                // "wizards"  => array(
                    // "_PADDING" => 2,
                    // "link"     => array(
                        // "type"         => "popup",
                        // "title"        => "Link",
                        // "icon"         => "link_popup.gif",
                        // "script"       => "browse_links.php?mode=wizard",
                        // "JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
                    // )
                // )
            // )
        // ),
        // 'image' => txdam_getMediaTCA('image_field', 'tx_t3blog_rollimage'),
		// 'description' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.description',
            // 'config' => Array (
                // 'type' => 'text',
                // 'cols' => '30',
                // 'rows' => '5',
                // 'wizards' => Array(
                    // '_PADDING' => 2,
                    // 'RTE' => array(
                        // 'notNewRecords' => 1,
                        // 'RTEonly' => 1,
                        // 'type' => 'script',
                        // 'title' => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
                        // 'icon' => 'wizard_rte2.gif',
                        // 'script' => 'wizard_rte.php',
                    // ),
                // ),
            // )
        // ),
         // 'xfn' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn',
            // 'config' => Array (
                // 'type' => 'select',
                // 'items' => Array (
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.0', '0'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.1', '1'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.2', '2'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.3', '3'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.4', '4'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.5', '5'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.6', '6'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.7', '7'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.8', '8'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.9', '9'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.10', '10'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.11', '11'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.12', '12'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.13', '13'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.14', '14'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.15', '15'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.16', '16'),
                    // Array('LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.xfn.I.17', '17'),
                // ),
                // 'size' => 5,
                // 'maxitems' => 99,
            // )
        // ),
    // ),
    // 'types' => array (
        // '0' => array('showitem' => '--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.generalTab;;;;1-1-1, title;;;;2-2-2, url;;;;3-3-3,--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.additionalTab,xfn, image, description;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts],--div--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.access,--palette--;LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll.access;1')
    // ),
    // 'palettes' => array (
        // '1' => array('showitem' => 'starttime, endtime, fe_group, hidden','canNotCollapse'=>1)
    // )
// );

// $TCA['tx_t3blog_pingback'] = array (
    // 'ctrl' => $TCA['tx_t3blog_pingback']['ctrl'],
    // 'interface' => array (
        // 'showRecordFieldList' => 'hidden,starttime,endtime,title,url,date,text'
    // ),
    // 'feInterface' => $TCA['tx_t3blog_pingback']['feInterface'],
    // 'columns' => array (
        // 'hidden' => array (
            // 'exclude' => 1,
            // 'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            // 'config'  => array (
                // 'type'    => 'check',
                // 'default' => '0'
            // )
        // ),
        // 'starttime' => array (
            // 'exclude' => 1,
            // 'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
            // 'config'  => array (
                // 'type'     => 'input',
                // 'size'     => '8',
                // 'max'      => '20',
                // 'eval'     => 'date',
                // 'default'  => '0',
                // 'checkbox' => '0'
            // )
        // ),
        // 'endtime' => array (
            // 'exclude' => 1,
            // 'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
            // 'config'  => array (
                // 'type'     => 'input',
                // 'size'     => '8',
                // 'max'      => '20',
                // 'eval'     => 'date',
                // 'checkbox' => '0',
                // 'default'  => '0',
                // 'range'    => array (
                    // 'upper' => mktime(0, 0, 0, 12, 31, 2020),
                    // 'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
                // )
            // )
        // ),
        // 'title' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_pingback.title',
            // 'config' => Array (
                // 'type' => 'input',
                // 'size' => '30',
            // )
        // ),
        // 'url' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_pingback.url',
            // 'config' => Array (
                // 'type' => 'input',
                // 'size' => '30',
            // )
        // ),
        // 'date' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_pingback.date',
            // 'config' => Array (
                // 'type'     => 'input',
                // 'size'     => '12',
                // 'max'      => '20',
                // 'eval'     => 'datetime',
                // 'checkbox' => '0',
                // 'default'  => '0'
            // )
        // ),
        // 'text' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_pingback.text',
            // 'config' => Array (
                // 'type' => 'text',
                // 'cols' => '30',
                // 'rows' => '5',
            // )
        // ),
    // ),
    // 'types' => array (
        // '0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, url;;;;3-3-3, date, text')
    // ),
    // 'palettes' => array (
        // '1' => array('showitem' => 'starttime, endtime')
    // )
// );

// $TCA['tx_t3blog_trackback'] = array (
    // 'ctrl' => $TCA['tx_t3blog_trackback']['ctrl'],
    // 'interface' => array (
        // 'showRecordFieldList' => 'hidden,fromurl,text,title,postid,id'
    // ),
    // 'feInterface' => $TCA['tx_t3blog_trackback']['feInterface'],
    // 'columns' => array (
        // 'hidden' => array (
            // 'exclude' => 1,
            // 'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            // 'config'  => array (
                // 'type'    => 'check',
                // 'default' => '0'
            // )
        // ),
        // 'fromurl' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_trackback.fromurl',
            // 'config' => Array (
                // 'type' => 'input',
                // 'size' => '30',
                // 'max' => '100',
                // 'eval' => 'trim',
            // )
        // ),
        // 'text' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_trackback.text',
            // 'config' => Array (
                // 'type' => 'input',
                // 'size' => '30',
                // 'max' => '255',
                // 'eval' => 'trim',
            // )
        // ),
        // 'title' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_trackback.title',
            // 'config' => Array (
                // 'type' => 'input',
                // 'size' => '30',
                // 'max' => '50',
                // 'eval' => 'trim',
            // )
        // ),
        // 'blogname' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_trackback.blogname',
            // 'config' => Array (
                // 'type' => 'input',
                // 'size' => '30',
                // 'max' => '50',
                // 'eval' => 'trim',
            // )
        // ),
        // 'postid' => Array (
            // 'exclude' => 1,
            // 'label' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_trackback.postid',
            // 'config' => Array (
                // 'type' => 'group',
                // 'internal_type' => 'db',
                // 'allowed' => 'tx_t3blog_post',
                // 'size' => 1,
                // 'minitems' => 0,
                // 'maxitems' => 1,
				// 'prepend_tname' => FALSE,
            // )
        // ),
    // ),
    // 'types' => array (
        // '0' => array('showitem' => 'hidden;;1;;1-1-1, fromurl, text, title;;;;2-2-2, postid;;;;3-3-3, id')
    // ),
    // 'palettes' => array (
        // '1' => array('showitem' => '')
    // )
// );
?>