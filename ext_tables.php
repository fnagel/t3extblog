<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY);


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
		'typeicon_classes' => [
			'default' => 'extensions-t3extblog-post',
		],
		'dividers2tabs' => TRUE,
		'searchFields' => 'title',
	),
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
		'typeicon_classes' => [
			'default' => 'extensions-t3extblog-category',
		],
		'dividers2tabs' => TRUE,
		'searchFields' => 'catname,description',
	),
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
		'typeicon_classes' => [
			'default' => 'extensions-t3extblog-comment',
		],
		'dividers2tabs' => TRUE,
		'searchFields' => 'title,author,email,website,text',
	),
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
		'typeicon_classes' => [
			'default' => 'extensions-t3extblog-subscriber',
		],
		'searchFields' => 'email,name',
	),
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
		'typeicon_classes' => [
			'default' => 'extensions-t3extblog-trackback',
		],
		'searchFields' => 'title,url,text,',
	),
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
		'typeicon_classes' => [
			'default' => 'extensions-t3extblog-trackback',
		],
		'searchFields' => 'title,fromurl,text,blogname',
	),
);

// Use old icon path for TYPO3 6.2
// @todo Remove this when 6.2 is no longer relevant
if (version_compare(TYPO3_branch, '7.0', '<')) {
	$GLOBALS['TCA']['tx_t3blog_post']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/page.png';
	$GLOBALS['TCA']['tx_t3blog_cat']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/category.png';
	$GLOBALS['TCA']['tx_t3blog_com']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/comment.png';
	$GLOBALS['TCA']['tx_t3blog_com_nl']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/subscriber.png';
	$GLOBALS['TCA']['tx_t3blog_pingback']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/trackback.png';
	$GLOBALS['TCA']['tx_t3blog_trackback']['ctrl']['iconfile'] = $extensionPath . 'Resources/Public/Icons/trackback.png';
}

if (TYPO3_MODE === 'BE') {
	// Add icons to registry
	// @todo Remove if statement when 6.2 is no longer relevant
	if (version_compare(TYPO3_branch, '7.6', '>=')) {
		/* @var $iconRegistry \TYPO3\CMS\Core\Imaging\IconRegistry */
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		$iconRegistry->registerIcon(
			'extensions-t3extblog-post',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:t3extblog/Resources/Public/Icons/page.png']
		);
		$iconRegistry->registerIcon(
			'extensions-t3extblog-category',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:t3extblog/Resources/Public/Icons/category.png']
		);
		$iconRegistry->registerIcon(
			'extensions-t3extblog-comment',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:t3extblog/Resources/Public/Icons/comment.png']
		);
		$iconRegistry->registerIcon(
			'extensions-t3extblog-subscriber',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:t3extblog/Resources/Public/Icons/subscriber.png']
		);
		$iconRegistry->registerIcon(
			'extensions-t3extblog-trackback',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:t3extblog/Resources/Public/Icons/trackback.png']
		);
		$iconRegistry->registerIcon(
			'tcarecords-pages-contains-t3blog',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:t3extblog/Resources/Public/Icons/folder.png']
		);
	}

	// Add BE page icon
	$pageModuleConfig = array(
		0 => 'T3extblog',
		1 => 't3blog',
		2 => 'tcarecords-pages-contains-t3blog'
	);

	if (version_compare(TYPO3_branch, '7.6', '>=')) {
		$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = $pageModuleConfig;
		$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-t3blog'] = 'tcarecords-pages-contains-t3blog';
	} else {
		// @todo Remove this when 6.2 is no longer relevant
		$pageModuleConfig[2] = '../typo3conf/ext/t3extblog/Resources/Public/Icons/folder.png';
		$addNewsToModuleSelection = TRUE;
		foreach ($GLOBALS['TCA']['pages']['columns']['module']['config']['items'] as $item) {
			if ($item[1] === 't3blog') {
				$addNewsToModuleSelection = FALSE;
				continue;
			}
		}
		if ($addNewsToModuleSelection) {
			$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = $pageModuleConfig;
		}
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
