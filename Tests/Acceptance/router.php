<?php

// PHP built-in server router for the TYPO3 acceptance test instance.
// $_SERVER['DOCUMENT_ROOT'] is set by the -t flag of `php -S`, so this file
// can live anywhere — it does not need to be inside the instance directory.

// Load autoloader before c3.php so Codeception classes are available.
// c3.php no-ops when no coverage cookie/header is present.
require_once __DIR__ . '/../../.Build/vendor/autoload.php';
include __DIR__ . '/../../c3.php';

$root = $_SERVER['DOCUMENT_ROOT'];
$file = $root . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (is_file($file)) {
    // Let the server serve static files (CSS, JS, images) directly.
    return false;
}
$_SERVER['SCRIPT_FILENAME'] = $root . '/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $root . '/index.php';
