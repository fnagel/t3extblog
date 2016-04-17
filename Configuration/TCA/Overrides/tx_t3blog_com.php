<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// @todo Remove this when 6.2 is no longer relevant
if (version_compare(TYPO3_branch, '7.0', '<')) {
	$GLOBALS['TCA']['tx_t3blog_com']['columns']['text']['config']['wizards']['RTE']['title'] =
		'LLL:EXT:cms/locallang_ttc.xml:bodytext.W.RTE';
	$GLOBALS['TCA']['tx_t3blog_com']['columns']['text']['config']['wizards']['RTE']['icon'] = 'wizard_rte2.gif';

	// Use old localization path
	$GLOBALS['TCA']['tx_t3blog_com']['types']['0']['showitem'] =
		str_replace('frontend/Resources/Private/Language', 'cms', $GLOBALS['TCA']['tx_t3blog_com']['types']['0']['showitem']);
	$GLOBALS['TCA']['tx_t3blog_com']['palettes']['access']['showitem'] =
		str_replace('frontend/Resources/Private/Language', 'cms', $GLOBALS['TCA']['tx_t3blog_com']['palettes']['access']['showitem']);

	// Add do not collapse
	$GLOBALS['TCA']['tx_t3blog_com']['palettes']['visibility']['canNotCollapse'] = TRUE;
	$GLOBALS['TCA']['tx_t3blog_com']['palettes']['access']['canNotCollapse'] = TRUE;
}
if (version_compare(TYPO3_branch, '7.0', '>')) {
	$GLOBALS['TCA']['tx_t3blog_com']['columns']['email']['config']['eval'] .= ',email';
}
