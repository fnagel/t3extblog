<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);


// Add static TS
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
	$_EXTKEY, 'Configuration/TypoScript', 'T3Extblog: Default setup (needed)'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
	$_EXTKEY, 'Configuration/TypoScript/Rss', 'T3Extblog: Rss setup'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
	$_EXTKEY, 'Configuration/TypoScript/RealUrl', 'T3Extblog: additional RealUrl config'
);


// Add Plugins and Flexforms
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'TYPO3.' . $_EXTKEY,
	'Blogsystem',
	'T3Blog Extbase: Blogsystem'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'TYPO3.' . $_EXTKEY,
	'SubscriptionManager',
	'T3Blog Extbase: Subscription Manager'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'TYPO3.' . $_EXTKEY,
	'Archive',
	'T3Blog Extbase: Archive'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'TYPO3.' . $_EXTKEY,
	'Rss',
	'T3Blog Extbase: RSS'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'TYPO3.' . $_EXTKEY,
	'Categories',
	'T3Blog Extbase: Categories'
);

$pluginSignature = strtolower($extensionName) . '_categories';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform,recursive';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	$pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/Categories.xml'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'TYPO3.' . $_EXTKEY,
	'LatestPosts',
	'T3Blog Extbase: LatestPosts'
);
$pluginSignature = strtolower($extensionName) . '_latestposts';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform,recursive';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	$pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/LatestPosts.xml'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'TYPO3.' . $_EXTKEY,
	'LatestComments',
	'T3Blog Extbase: LatestComments'
);
$pluginSignature = strtolower($extensionName) . '_latestcomments';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform,recursive';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	$pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/LatestComments.xml'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_post');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_t3blog_post');
$GLOBALS['TCA']['tx_t3blog_post'] = array(
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
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/Tca/Post.php',
		'iconfile' => 'EXT:t3extblog/Resources/Public/Icons/page.png',
		'dividers2tabs' => TRUE,
		'searchFields' => 'title',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, title, author, date, content,allow_comments, cat, trackback,number_views',
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_cat');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_t3blog_cat');
$GLOBALS['TCA']['tx_t3blog_cat'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_cat',
		'label' => 'catname',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.xlf:LGL.prependAtCopy',
		'hideAtCopy' => TRUE,
		'treeParentField' => 'parent_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/Tca/Category.php',
		'iconfile' => 'EXT:t3extblog/Resources/Public/Icons/category.png',
		'dividers2tabs' => TRUE,
		'searchFields' => 'catname,description',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, parent_id, catname, description',
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_com');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_t3blog_com');
$GLOBALS['TCA']['tx_t3blog_com'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com',
		'label' => 'title',
		'label_alt' => 'fk_post',
		'label_alt_force' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate DESC',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/Tca/Comment.php',
		'iconfile' => 'EXT:t3extblog/Resources/Public/Icons/comment.png',
		'dividers2tabs' => TRUE,
		'searchFields' => 'title,author,email,website,text',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, starttime, endtime, fe_group, title, author, email, website, date, text, approved, spam, fk_post, mails_sent',
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_com_nl');
$GLOBALS['TCA']['tx_t3blog_com_nl'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_com_nl',
		'label' => 'email',
		'label_alt' => 'post_uid',
		'label_alt_force' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'default_sortby' => 'ORDER BY crdate DESC',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) .
			'Configuration/Tca/Subscriber.php',
		'iconfile' => 'EXT:t3extblog/Resources/Public/Icons/subscriber.png',
		'searchFields' => 'email,name',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, email, name, lastsent, post_uid, code',
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_pingback');
$GLOBALS['TCA']['tx_t3blog_pingback'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_pingback',
		'label' => 'uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) .
			'Configuration/Tca/Pingback.php',
		'iconfile' => 'EXT:t3extblog/Resources/Public/Icons/trackback.png',
		'searchFields' => 'title,url,text,',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, starttime, endtime, title, url, date, text',
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_trackback');
$GLOBALS['TCA']['tx_t3blog_trackback'] = array(
	'ctrl' => array(
		'title' => 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_db.xml:tx_t3blog_trackback',
		'label' => 'uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) .
			'Configuration/Tca/Trackback.php',
		'iconfile' => 'EXT:t3extblog/Resources/Public/Icons/trackback.png',
		'searchFields' => 'title,fromurl,text,blogname',
	),
	'feInterface' => array(
		'fe_admin_fieldList' => 'hidden, fromurl, text, title, postid, id',
	)
);

// Use old icon path for TYPO3 6.2
// @todo Remove this when 6.2 is no longer relevant
if (version_compare(TYPO3_branch, '7.0', '<')) {
	$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY);

	$GLOBALS['TCA']['tx_t3blog_post']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/page.png';
	$GLOBALS['TCA']['tx_t3blog_cat']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/category.png';
	$GLOBALS['TCA']['tx_t3blog_com']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/comment.png';
	$GLOBALS['TCA']['tx_t3blog_com_nl']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/subscriber.png';
	$GLOBALS['TCA']['tx_t3blog_pingback']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/trackback.png';
	$GLOBALS['TCA']['tx_t3blog_trackback']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/trackback.png';
}


unset($GLOBALS['ICON_TYPES']['t3blog']);
\TYPO3\CMS\Backend\Sprite\SpriteManager::addTcaTypeIcon(
	'pages', 'contains-t3blog', '../typo3conf/ext/t3extblog/Resources/Public/Icons/folder.png'
);

if (TYPO3_MODE === 'BE') {
	// Add BE page icon
	$addNewsToModuleSelection = TRUE;
	foreach ($GLOBALS['TCA']['pages']['columns']['module']['config']['items'] as $item) {
		if ($item[1] === 't3blog') {
			$addNewsToModuleSelection = FALSE;
			continue;
		}
	}
	if ($addNewsToModuleSelection) {
		$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = array(
			0 => 'T3extblog',
			1 => 't3blog',
			2 => '../typo3conf/ext/t3extblog/Resources/Public/Icons/folder.png'
		);
	}

	// @todo Remove this when 6.2 is no longer relevant
	$icon = '/Resources/Public/Icons/module.png';
	if (version_compare(TYPO3_branch, '7.0', '<')) {
		// Use a smaller icon for TYPO3 6.2
		$icon = '/ext_icon.gif';
	}

	// Register  Backend Module
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'TYPO3.' . $_EXTKEY,
		'web',
		'Tx_T3extblog',
		'',
		array(
			'BackendPost' => 'index',
			'BackendComment' => 'index, listPending, listByPost'
		),
		array(
			'access' => 'user,group',
			'icon' => 'EXT:' . $_EXTKEY . $icon,
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml',
			'navigationComponentId' => 'typo3-pagetree',
		)
	);
}
