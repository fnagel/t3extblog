<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$pageModuleConfig = [
    0 => 'T3extblog',
    1 => 't3blog',
    2 => 'tcarecords-pages-contains-t3blog',
];
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = $pageModuleConfig;
$GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-t3blog'] = 'tcarecords-pages-contains-t3blog';
