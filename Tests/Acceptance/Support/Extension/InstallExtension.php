<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Acceptance\Support\Extension;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Codeception\Events;
use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;

/**
 * Bootstraps a full TYPO3 test instance with t3extblog installed and
 * loaded with acceptance test fixtures. Used once per suite.
 *
 * The instance is created in typo3temp/ and served by a PHP built-in
 * web server that is started automatically after instance setup.
 */
final class InstallExtension extends BackendEnvironment
{
    protected $localConfig = [
        'typo3DatabaseDriver' => 'pdo_sqlite',
        'coreExtensionsToLoad' => [
            'core',
            'backend',
            'extbase',
            'fluid',
            'frontend',
            'seo',
            'dashboard',
        ],
        'testExtensionsToLoad' => [
            'felixnagel/t3extblog',
        ],
        'csvDatabaseFixtures' => [
            __DIR__ . '/../../Fixtures/pages.csv',
            __DIR__ . '/../../Fixtures/sys_template.csv',
            __DIR__ . '/../../Fixtures/tt_content.csv',
            __DIR__ . '/../../Fixtures/be_users.csv',
            __DIR__ . '/../../Fixtures/be_sessions.csv',
            __DIR__ . '/../../Fixtures/posts.csv',
            __DIR__ . '/../../Fixtures/comments.csv',
            __DIR__ . '/../../Fixtures/be_dashboards.csv',
            __DIR__ . '/../../Fixtures/post_subscribers.csv',
        ],
        'configurationToUseInTestInstance' => [
            'SYS' => [
                'encryptionKey' => 'acetestacetestacetestacetestacetestacetestacetestacetest',
            ],
            'MAIL' => [
                'transport' => 'null',
            ],
            'FE' => [
                'pageNotFoundOnCHashError' => false,
                'cacheHash' => [
                    'enforceValidation' => false,
                ],
            ],
        ],
        'pathsToLinkInTestInstance' => [
            'typo3conf/ext/t3extblog/Tests/Acceptance/Fixtures/sites' => 'typo3conf/sites',
        ],
    ];
}
