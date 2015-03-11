<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "t3extblog".
 *
 * Auto generated 18-02-2014 01:36
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'T3Blog Extbase',
	'description' => 'A flexible blog extension powered by Extbase / Fluid which aims to replace t3blog.',
	'category' => 'plugin',
	'author' => 'Felix Nagel',
	'author_email' => 'info@felixnagel.com',
	'author_company' => '',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.9.1',
	'constraints' => array(
		'depends' => array(
			'extbase' => '1.5',
			'fluid' => '1.5',
			'typo3' => '4.5.0-6.2.99',
		),
		'conflicts' => array(
			't3blog' => '',
		),
		'suggests' => array(
			'sfpantispam' => '',
			'dd_googlesitemap' => '1.3.1-1.3.99',
		),
	),
	'_md5_values_when_last_written' => 'a:101:{s:12:"ext_icon.gif";s:4:"e922";s:17:"ext_localconf.php";s:4:"1ea3";s:14:"ext_tables.php";s:4:"91bb";s:14:"ext_tables.sql";s:4:"c1c5";s:24:"ext_typoscript_setup.txt";s:4:"7cb0";s:21:"ExtensionBuilder.json";s:4:"e731";s:9:"README.md";s:4:"9612";s:7:"tca.php";s:4:"029c";s:41:"Classes/Controller/AbstractController.php";s:4:"34e3";s:44:"Classes/Controller/BackendBaseController.php";s:4:"4a16";s:47:"Classes/Controller/BackendCommentController.php";s:4:"8917";s:44:"Classes/Controller/BackendPostController.php";s:4:"8b87";s:41:"Classes/Controller/CategoryController.php";s:4:"f087";s:40:"Classes/Controller/CommentController.php";s:4:"4a1e";s:37:"Classes/Controller/PostController.php";s:4:"2c75";s:43:"Classes/Controller/SubscriberController.php";s:4:"fcea";s:36:"Classes/Domain/Model/BackendUser.php";s:4:"7f58";s:33:"Classes/Domain/Model/Category.php";s:4:"db71";s:32:"Classes/Domain/Model/Comment.php";s:4:"aaca";s:32:"Classes/Domain/Model/Content.php";s:4:"1e58";s:29:"Classes/Domain/Model/Post.php";s:4:"12ce";s:35:"Classes/Domain/Model/Subscriber.php";s:4:"c671";s:51:"Classes/Domain/Repository/BackendUserRepository.php";s:4:"b4d9";s:48:"Classes/Domain/Repository/CategoryRepository.php";s:4:"8ee9";s:47:"Classes/Domain/Repository/CommentRepository.php";s:4:"0650";s:44:"Classes/Domain/Repository/PostRepository.php";s:4:"f2ba";s:50:"Classes/Domain/Repository/SubscriberRepository.php";s:4:"b459";s:35:"Classes/Hooks/RealUrlAutoConfig.php";s:4:"bf11";s:25:"Classes/Hooks/Tcemain.php";s:4:"8955";s:39:"Classes/Service/FrontendUserService.php";s:4:"65ca";s:34:"Classes/Service/LoggingService.php";s:4:"7337";s:39:"Classes/Service/NotificationService.php";s:4:"7e62";s:35:"Classes/Service/SettingsService.php";s:4:"51ac";s:36:"Classes/Service/SpamCheckService.php";s:4:"5894";s:22:"Classes/Tca/T3blog.php";s:4:"696c";s:58:"Classes/Utility/class.tx_t3blog_tcefunc_selecttreeview.php";s:4:"4c87";s:44:"Classes/Utility/class.tx_t3blog_treeview.php";s:4:"056f";s:45:"Classes/Validation/Validator/UrlValidator.php";s:4:"fc23";s:46:"Classes/ViewHelpers/IssueCommandViewHelper.php";s:4:"8468";s:53:"Classes/ViewHelpers/SpriteIconForRecordViewHelper.php";s:4:"41c8";s:51:"Classes/ViewHelpers/SpriteManagerIconViewHelper.php";s:4:"2dd4";s:53:"Classes/ViewHelpers/Frontend/BaseRenderViewHelper.php";s:4:"41fa";s:57:"Classes/ViewHelpers/Frontend/CommentAllowedViewHelper.php";s:4:"ab44";s:56:"Classes/ViewHelpers/Frontend/FlashMessagesViewHelper.php";s:4:"c301";s:51:"Classes/ViewHelpers/Frontend/GravatarViewHelper.php";s:4:"4cee";s:56:"Classes/ViewHelpers/Frontend/RenderContentViewHelper.php";s:4:"5481";s:56:"Classes/ViewHelpers/Frontend/RenderPreviewViewHelper.php";s:4:"74d4";s:51:"Classes/ViewHelpers/Frontend/TitleTagViewHelper.php";s:4:"c002";s:61:"Classes/ViewHelpers/Widgets/Controller/PaginateController.php";s:4:"476e";s:44:"Configuration/ExtensionBuilder/settings.yaml";s:4:"51c3";s:38:"Configuration/TypoScript/constants.txt";s:4:"4da1";s:34:"Configuration/TypoScript/setup.txt";s:4:"3986";s:38:"Configuration/TypoScript/Rss/setup.txt";s:4:"655c";s:46:"Resources/Private/Backend/Layouts/Default.html";s:4:"af6a";s:51:"Resources/Private/Backend/Partials/Comment/Row.html";s:4:"ec6d";s:53:"Resources/Private/Backend/Partials/Comment/Table.html";s:4:"29b2";s:48:"Resources/Private/Backend/Partials/Post/Row.html";s:4:"9653";s:61:"Resources/Private/Backend/Templates/BackendComment/Index.html";s:4:"1322";s:60:"Resources/Private/Backend/Templates/BackendComment/List.html";s:4:"cfee";s:58:"Resources/Private/Backend/Templates/BackendPost/Index.html";s:4:"8941";s:43:"Resources/Private/Language/de.locallang.xlf";s:4:"fffb";s:40:"Resources/Private/Language/locallang.xlf";s:4:"1ac4";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"a745";s:44:"Resources/Private/Language/locallang_mod.xlf";s:4:"33a8";s:38:"Resources/Private/Layouts/Default.html";s:4:"3086";s:42:"Resources/Private/Partials/FormErrors.html";s:4:"9e9c";s:50:"Resources/Private/Partials/Comment/FormFields.html";s:4:"b08c";s:43:"Resources/Private/Partials/Comment/New.html";s:4:"b3dd";s:44:"Resources/Private/Partials/Comment/Show.html";s:4:"fb61";s:44:"Resources/Private/Partials/Comment/Spam.html";s:4:"9a4a";s:47:"Resources/Private/Partials/Post/Categories.html";s:4:"846f";s:43:"Resources/Private/Partials/Post/Filter.html";s:4:"1f28";s:45:"Resources/Private/Partials/Post/TagCloud.html";s:4:"83f0";s:46:"Resources/Private/Templates/Category/List.html";s:4:"9838";s:46:"Resources/Private/Templates/Category/Show.html";s:4:"8cdf";s:45:"Resources/Private/Templates/Comment/Edit.html";s:4:"3073";s:47:"Resources/Private/Templates/Comment/Latest.html";s:4:"47ad";s:45:"Resources/Private/Templates/Comment/List.html";s:4:"19bf";s:44:"Resources/Private/Templates/Comment/New.html";s:4:"04d7";s:57:"Resources/Private/Templates/Email/AdminNewCommentMail.txt";s:4:"8b7f";s:62:"Resources/Private/Templates/Email/SubscriberNewCommentMail.txt";s:4:"9af2";s:57:"Resources/Private/Templates/Email/SubscriberOptinMail.txt";s:4:"145b";s:45:"Resources/Private/Templates/Post/Archive.html";s:4:"b414";s:44:"Resources/Private/Templates/Post/Latest.html";s:4:"f004";s:42:"Resources/Private/Templates/Post/List.html";s:4:"77a2";s:40:"Resources/Private/Templates/Post/Rss.xml";s:4:"d47d";s:42:"Resources/Private/Templates/Post/Show.html";s:4:"f52e";s:49:"Resources/Private/Templates/Subscriber/Error.html";s:4:"66f1";s:48:"Resources/Private/Templates/Subscriber/List.html";s:4:"1c97";s:66:"Resources/Private/Templates/ViewHelpers/Widget/Paginate/Index.html";s:4:"703f";s:37:"Resources/Public/Css/BackendStyle.css";s:4:"d572";s:45:"Resources/Public/Icons/chart_organisation.png";s:4:"c8df";s:34:"Resources/Public/Icons/comment.png";s:4:"a520";s:50:"Resources/Public/Icons/icon_tx_t3blog_blogroll.png";s:4:"16fb";s:51:"Resources/Public/Icons/icon_tx_t3blog_trackback.gif";s:4:"ac79";s:51:"Resources/Public/Icons/icon_tx_t3blog_trackback.png";s:4:"46a9";s:44:"Tests/Unit/Controller/PostControllerTest.php";s:4:"c2b5";s:36:"Tests/Unit/Domain/Model/BaseTest.php";s:4:"696d";s:39:"Tests/Unit/Domain/Model/CommentTest.php";s:4:"da6d";s:36:"Tests/Unit/Domain/Model/PostTest.php";s:4:"bb3d";s:42:"Tests/Unit/Domain/Model/SubscriberTest.php";s:4:"466c";}',
);

?>