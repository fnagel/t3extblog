<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// @todo Remove this when 6.2 is no longer relevant
if (version_compare(TYPO3_branch, '7.0', '<')) {
	$GLOBALS['TCA']['tx_t3blog_cat']['columns']['parent_id']['config']['renderMode'] = 'tree';

	// Use old localization path
	$GLOBALS['TCA']['tx_t3blog_cat']['types']['0']['showitem'] =
		str_replace('frontend/Resources/Private/Language', 'cms', $GLOBALS['TCA']['tx_t3blog_cat']['types']['0']['showitem']);
	$GLOBALS['TCA']['tx_t3blog_cat']['palettes']['access']['showitem'] =
		str_replace('frontend/Resources/Private/Language', 'cms', $GLOBALS['TCA']['tx_t3blog_cat']['palettes']['access']['showitem']);

	// Add do not collapse
	$GLOBALS['TCA']['tx_t3blog_cat']['palettes']['visibility']['canNotCollapse'] = TRUE;
	$GLOBALS['TCA']['tx_t3blog_cat']['palettes']['access']['canNotCollapse'] = TRUE;
}
