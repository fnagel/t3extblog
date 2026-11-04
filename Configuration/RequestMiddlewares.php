<?php

use FelixNagel\T3extblog\Middleware\ResetPageTitleMiddleware;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$middlewares = [];

// Only needed for frontend indexing of EXT:index
if (ExtensionManagementUtility::isLoaded('index')) {
    $middlewares['frontend']['t3extblog/reset-page-title'] = [
        'target' => ResetPageTitleMiddleware::class,
        'after' => [
            'typo3/cms-frontend/site',
        ],
        'before' => [
            'typo3/cms-frontend/page-resolver',
        ],
    ];
}

return $middlewares;
