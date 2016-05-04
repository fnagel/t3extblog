<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

call_user_func(function($packageKey) {
	$extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($packageKey);
	$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($packageKey);

	// Add static TS
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
		$packageKey, 'Configuration/TypoScript', 'T3Extblog: Default setup (needed)'
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
		$packageKey, 'Configuration/TypoScript/Rss', 'T3Extblog: Rss setup'
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
		$packageKey, 'Configuration/TypoScript/RealUrl', 'T3Extblog: additional RealUrl config'
	);

	// Add page TS config
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
		'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:t3extblog/Configuration/TypoScript/pageTsConfig.ts">'
	);

	// Add Plugins and Flexforms
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'TYPO3.' . $packageKey,
		'Blogsystem',
		'T3Blog Extbase: Blogsystem'
	);

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'TYPO3.' . $packageKey,
		'SubscriptionManager',
		'T3Blog Extbase: Subscription Manager'
	);

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'TYPO3.' . $packageKey,
		'BlogSubscription',
		'T3Blog Extbase: Blog Subscription Form'
	);

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'TYPO3.' . $packageKey,
		'Archive',
		'T3Blog Extbase: Archive'
	);

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'TYPO3.' . $packageKey,
		'Rss',
		'T3Blog Extbase: RSS'
	);

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'TYPO3.' . $packageKey,
		'Categories',
		'T3Blog Extbase: Categories'
	);

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'TYPO3.' . $packageKey,
		'LatestPosts',
		'T3Blog Extbase: LatestPosts'
	);
	$pluginSignature = strtolower($extensionName) . '_latestposts';
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key';
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform,recursive';
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
		$pluginSignature, 'FILE:EXT:' . $packageKey . '/Configuration/FlexForms/LatestPosts.xml'
	);

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
		'TYPO3.' . $packageKey,
		'LatestComments',
		'T3Blog Extbase: LatestComments'
	);


	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_post');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_t3blog_post');

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_cat');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_t3blog_cat');

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_com');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_t3blog_com');

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_com_nl');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_blog_nl');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_pingback');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_t3blog_trackback');


	if (TYPO3_MODE === 'BE') {
		$pageModuleConfig = array(
			0 => 'T3extblog',
			1 => 't3blog',
			2 => 'tcarecords-pages-contains-t3blog'
		);

		// Add icons to registry
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

		// Add BE page icon
		$iconRegistry->registerIcon(
			'tcarecords-pages-contains-t3blog',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:t3extblog/Resources/Public/Icons/folder.png']
		);
		$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = $pageModuleConfig;
		$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-t3blog'] = 'tcarecords-pages-contains-t3blog';

		// Register  Backend Module
		\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
			'TYPO3.' . $packageKey,
			'web',
			'Tx_T3extblog',
			'',
			array(
				'BackendDashboard' => 'index',
				'BackendPost' => 'index, sendPostNotifications',
				'BackendComment' => 'index, listPending, listByPost',
				'BackendSubscriber' => 'indexPostSubscriber, indexBlogSubscriber'
			),
			array(
				'access' => 'user,group',
				'icon' => 'EXT:' . $packageKey . '/Resources/Public/Icons/module.png',
				'labels' => 'LLL:EXT:' . $packageKey . '/Resources/Private/Language/locallang_mod.xml',
				'navigationComponentId' => 'typo3-pagetree',
			)
		);
	}

}, $_EXTKEY );
