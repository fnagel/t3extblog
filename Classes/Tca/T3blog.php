<?php

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

if (version_compare(TYPO3_branch, '6.1', '<')) {
	t3lib_div::loadTCA('pages');
	t3lib_div::loadTCA('be_users');
	t3lib_div::loadTCA('tt_content');
}

t3lib_extMgm::allowTableOnStandardPages('tx_t3blog_post');
$TCA['pages']['columns']['module']['config']['items'][] = Array('T3Blog', 't3blog');
t3lib_extMgm::addToInsertRecords('tx_t3blog_post');

$TCA['tx_t3blog_post'] = array (
	'ctrl' => array (
		'title'     			=> 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post',
		'label'     			=> 'title',
		'tstamp'    			=> 'tstamp',
		'crdate'    			=> 'crdate',
		//'cruser_id' 			=> 'author',
		'versioningWS' 			=> TRUE,
		'origUid' 				=> 't3_origuid',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'default_sortby' 		=> 'ORDER BY crdate DESC',
		'delete' 				=> 'deleted',
		'enablecolumns' 		=> array (
			'disabled' 	=> 'hidden',
			'starttime' => 'starttime',
			'endtime' 	=> 'endtime',
			'fe_group' 	=> 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'Resources/Public/Icons/page.png',
		'dividers2tabs'			=>	TRUE,
		'searchFields' => 'title',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, title, author, date, content,allow_comments, cat, trackback,number_views',
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_t3blog_cat');
t3lib_extMgm::addToInsertRecords('tx_t3blog_cat');

$TCA['tx_t3blog_cat'] = array (
	'ctrl' => array (
		'title'     				=> 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_cat',
		'label'     				=> 'catname',
		'tstamp'    				=> 'tstamp',
		'crdate'    				=> 'crdate',
		'cruser_id' 				=> 'cruser_id',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'treeParentField' 			=> 'parent_id',
		'sortby' 					=> 'sorting',
		'delete' 					=> 'deleted',
		'enablecolumns' 			=> array (
			'disabled' 	=> 'hidden',
			'starttime' => 'starttime',
			'endtime' 	=> 'endtime',
			'fe_group' 	=> 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY). 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY). 'Resources/Public/Icons/chart_organisation.png',
		'dividers2tabs'			=>	TRUE,
		'searchFields' => 'catname,description',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, parent_id, catname, description',
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_t3blog_com');
t3lib_extMgm::addToInsertRecords('tx_t3blog_com');

$TCA['tx_t3blog_com'] = array (
	'ctrl' 	=> array (
		'title'     		=> 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com',
		'tagClouds'     	=> 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_post',
		'label'     		=> 'title',
		'tstamp'    		=> 'tstamp',
		'crdate'    		=> 'crdate',
		'cruser_id' 		=> 'cruser_id',
		'default_sortby' 	=> 'ORDER BY crdate DESC',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array (
			'disabled' 		=> 'hidden',
			'starttime' 	=> 'starttime',
			'endtime' 		=> 'endtime',
			'fe_group' 		=> 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY). 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY). 'Resources/Public/Icons/comment.png',
		'searchFields' => 'title,author,email,website,text',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, title, author, email, website, date, text, approved, spam, fk_post',
	)
);

$TCA['tx_t3blog_com_nl'] = array (
	'ctrl' 	=> array(
		'title'     		=> 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl',
		'label'     		=> 'name',
		'delete' 			=> 'deleted',
		'enablecolumns'     => array(
			'disable' => 'deleted',
		),
		'hideTable'			=> true,
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY). 'tca.php'
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_t3blog_blogroll');
t3lib_extMgm::addToInsertRecords('tx_t3blog_blogroll');

$TCA['tx_t3blog_blogroll'] = array (
	'ctrl' => array (
		'title'     				=> 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_blogroll',
		'label'     				=> 'title',
		'tstamp'    				=> 'tstamp',
		'crdate'    				=> 'crdate',
		'cruser_id' 				=> 'cruser_id',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField'            => 'sys_language_uid',
		'transOrigPointerField'    => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'sortby' 					=> 'sorting',
		'delete' 					=> 'deleted',
		'enablecolumns' 			=> array (
			'disabled' 	=> 'hidden',
			'starttime' => 'starttime',
			'endtime' 	=> 'endtime',
			'fe_group' 	=> 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY). 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'Resources/Public/Icons/icon_tx_t3blog_blogroll.png',
		'dividers2tabs'			=>	TRUE,
		'searchFields' => 'title,description',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, title, url, image, description, xfn ',
	)
);

$TCA['tx_t3blog_pingback'] = array (
	'ctrl' => array (
		'title'     		=> 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_pingback',
		'label'     		=> 'uid',
		'tstamp'    		=> 'tstamp',
		'crdate'    		=> 'crdate',
		'cruser_id' 		=> 'cruser_id',
		'sortby' 			=> 'sorting',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array (
			'disabled' 		=> 'hidden',
			'starttime' 	=> 'starttime',
			'endtime' 		=> 'endtime',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY). 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY). 'Resources/Public/Icons/icon_tx_t3blog_pingback.gif',
		'searchFields' => 'title,url,text,',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, starttime, endtime, title, url, date, text',
	)
);

$TCA['tx_t3blog_trackback'] = array (
	'ctrl' => array (
		'title'     		=> 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_trackback',
		'label'     		=> 'uid',
		'tstamp'    		=> 'tstamp',
		'crdate'    		=> 'crdate',
		'cruser_id' 		=> 'cruser_id',
		'default_sortby' 	=> 'ORDER BY crdate',
		'delete' 			=> 'deleted',
		'enablecolumns' 	=> array (
			'disabled' 	=> 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY). 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY). 'Resources/Public/Icons/icon_tx_t3blog_trackback.gif',
		'searchFields' => 'title,fromurl,text,blogname',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, fromurl, text, title, postid, id',
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_t3blog_trackback');

//if (TYPO3_MODE == 'BE')	{
//	$extPath = t3lib_extMgm::extPath($_EXTKEY);
//
//	if (! isset($TBE_MODULES['txt3blogM1']))	{
//		$temp_TBE_MODULES = array();
//
//		foreach($TBE_MODULES as $key => $val) {
//			if ($key == 'web') {
//				$temp_TBE_MODULES[$key] = $val;
//				$temp_TBE_MODULES['txt3blogM1'] = '';
//			} else {
//				$temp_TBE_MODULES[$key] = $val;
//			}
//		}
//		$TBE_MODULES = $temp_TBE_MODULES;
//	}
//	t3lib_extMgm::addModule('txt3blogM1', '', '', t3lib_extMgm::extPath($_EXTKEY). 'mod1/');
//	t3lib_extMgm::addModule('txt3blogM1', 'txt3blogM2', 'bottom', $extPath. 'mod2/');
//	t3lib_extMgm::addModule('txt3blogM1', 'txt3blogM3', 'bottom', $extPath. 'mod3/');
//	t3lib_extMgm::addModule('txt3blogM1', 'txt3blogM4', 'bottom', $extPath. 'mod4/');
//	t3lib_extMgm::addModule('txt3blogM1', 'txt3blogM5', 'bottom', $extPath. 'mod5/');
//	t3lib_extMgm::addModule('txt3blogM1', 'txt3blogM6', 'bottom', $extPath. 'mod6/');
//}

// the static templates
//t3lib_extMgm::addStaticFile($_EXTKEY, 'static/t3blog/pi1', 'T3BLOG - main configuration');
//t3lib_extMgm::addStaticFile($_EXTKEY, 'static/t3blog/styling/', 'T3BLOG CSS - snowflake theme 1');
//t3lib_extMgm::addStaticFile($_EXTKEY, 'static/t3blog/template/', 'T3BLOG template - snowflake theme 1 ');
//t3lib_extMgm::addStaticFile($_EXTKEY, 'static/t3blog/', 'T3BLOG blog2page - output to the page');
//t3lib_extMgm::addStaticFile($_EXTKEY, 'static/t3blog/pi2/', 'T3BLOG functionalities on your website');

if (TYPO3_MODE == 'BE')	{
	require_once(t3lib_extMgm::extPath($_EXTKEY). 'Classes/Utility/class.tx_t3blog_treeview.php');
	require_once(t3lib_extMgm::extPath($_EXTKEY). 'Classes/Utility/class.tx_t3blog_tcefunc_selecttreeview.php');
}

// be_users modification, to upload an image/avatar
//$tx_t3blog_avatar = Array(
//	'tx_t3blog_avatar' => txdam_getMediaTCA('image_field', 'tx_t3blog_avatar'),
//);
//t3lib_extMgm::addTCAcolumns('be_users', $tx_t3blog_avatar);
//t3lib_extMgm::addToAllTCATypes('be_users', 'tx_t3blog_avatar', '', 'after:realName');


//$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY. '_pi2'] = 'layout,select_key';

//t3lib_extMgm::addPlugin(array('LLL:EXT:t3blog/locallang_db.xml:tt_content.list_type_pi2', $_EXTKEY. '_pi2'), 'list_type');

//Flexform
//$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY. '_pi2'] = 'layout,select_key,pages,recursive';
//$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY. '_pi2'] = 'pi_flexform';

//if (TYPO3_MODE=='BE')	{
//	include_once(t3lib_extMgm::extPath($_EXTKEY). 'pi2/class.tx_t3blog_pi2_addFieldsToFlexForm.php');
//}
//t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi2', 'FILE:EXT:'. $_EXTKEY. '/flexform_pi2.xml');
//
//if (TYPO3_MODE=='BE')	{
//	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_t3blog_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY). 'pi2/class.tx_t3blog_pi2_wizicon.php';
//}

?>