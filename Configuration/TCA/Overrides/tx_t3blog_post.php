<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Add new palette type to hide link field for preview image
$GLOBALS['TCA']['sys_file_reference']['palettes']['t3extblogPostPreviewImagePalette'] = array(
	'showitem' => 'title, alternative, --linebreak--, description',
	'canNotCollapse' => TRUE
);

// @todo Remove this when 6.2 is no longer relevant
if (version_compare(TYPO3_branch, '7.2', '>=')) {
	// Enable cropping for newer TYPO3
	$GLOBALS['TCA']['sys_file_reference']['palettes']['t3extblogPostPreviewImagePalette']['showitem'] .= ', --linebreak--, crop';
}

// @todo Remove this when 6.2 is no longer relevant
if (version_compare(TYPO3_branch, '7.0', '<')) {
	$GLOBALS['TCA']['tx_t3blog_post']['columns']['cat']['config']['renderMode'] = 'tree';

	// Use old localization path
	$GLOBALS['TCA']['tx_t3blog_post']['types']['0']['showitem'] =
		str_replace('frontend/Resources/Private/Language', 'cms', $GLOBALS['TCA']['tx_t3blog_post']['types']['0']['showitem']);
	$GLOBALS['TCA']['tx_t3blog_post']['palettes']['access']['showitem'] =
		str_replace('frontend/Resources/Private/Language', 'cms', $GLOBALS['TCA']['tx_t3blog_post']['palettes']['access']['showitem']);

	// Add do not collapse
	$GLOBALS['TCA']['tx_t3blog_post']['palettes']['visibility']['canNotCollapse'] = TRUE;
	$GLOBALS['TCA']['tx_t3blog_post']['palettes']['access']['canNotCollapse'] = TRUE;
}
