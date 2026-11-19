<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Controller;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Frontend\Page\CacheHashCalculator;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext;

/**
 * Pages with a prefilled comment form contain personal data (email, name) and must not be cached,
 * otherwise the cached page would be delivered to other users.
 */
final class CommentPrefillCacheTest extends AbstractControllerTestCase
{
    protected array $configurationToUseInTestInstance = [
        'MAIL' => [
            'transport' => 'null',
        ],
        'FE' => [
            'pageNotFoundOnCHashError' => false,
            'cacheHash' => [
                'enforceValidation' => false,
            ],
        ],
        'SYS' => [
            'caching' => [
                'cacheConfigurations' => [
                    // The testing framework uses a NullBackend by default, restore the core default
                    'pages' => [
                        'backend' => Typo3DatabaseBackend::class,
                    ],
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/comments.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/categories.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/fe_groups.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/fe_users.csv');

        $this->setUpFrontendWithTypoScript();

    }

    #[Test]
    public function pageIsCachedWhenPrefillIsDisabled(): void
    {
        // Ensures the page cache is working in this test setup, see pageWithPrefilledCommentFormIsNotCached
        $this->addTypoScriptToTemplateRecord(
            1,
            'config.no_cache = 0'
            . "\n" . 'plugin.tx_t3extblog.settings.blogsystem.comments.prefillFields.enable = 0'
        );

        $this->executeFrontendSubRequest(
            $this->buildShowRequest(),
            (new InternalRequestContext())->withFrontendUserId(1)
        );

        self::assertGreaterThan(0, $this->countPageCacheEntries());
    }

    #[Test]
    public function pageWithPrefilledCommentFormIsNotCached(): void
    {
        $this->addTypoScriptToTemplateRecord(
            1,
            'config.no_cache = 0'
            . "\n" . 'plugin.tx_t3extblog.settings.blogsystem.comments.prefillFields.enable = 1'
        );

        $response = $this->executeFrontendSubRequest(
            $this->buildShowRequest(),
            (new InternalRequestContext())->withFrontendUserId(1)
        );

        self::assertStringContainsString('testuser@example.com', (string)$response->getBody());
        self::assertSame(0, $this->countPageCacheEntries());
    }

    protected function buildShowRequest(): InternalRequest
    {
        $parameters = [
            'tx_t3extblog_blogsystem[controller]' => 'Post',
            'tx_t3extblog_blogsystem[action]' => 'show',
            'tx_t3extblog_blogsystem[post]' => '1',
        ];

        // Without a valid cHash the page would never be cached
        $cacheHash = $this->get(CacheHashCalculator::class)
            ->generateForParameters(HttpUtility::buildQueryString(['id' => 1] + $parameters, '&'));

        $request = (new InternalRequest())->withPageId(1)->withQueryParameter('cHash', $cacheHash);
        foreach ($parameters as $name => $value) {
            $request = $request->withQueryParameter($name, $value);
        }

        return $request;
    }

    protected function countPageCacheEntries(): int
    {
        $backend = $this->get(CacheManager::class)->getCache('pages')->getBackend();

        return count($backend->findIdentifiersByTag('pageId_1'));
    }
}
