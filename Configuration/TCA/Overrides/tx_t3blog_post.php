<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Add new palette type to hide link field for preview image
$GLOBALS['TCA']['sys_file_reference']['palettes']['t3extblogPostPreviewImagePalette'] = array(
	'showitem' => 'title, alternative, --linebreak--, description, --linebreak--, crop',
	'canNotCollapse' => TRUE
);
