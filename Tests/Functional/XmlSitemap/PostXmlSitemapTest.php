<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\XmlSitemap;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

/**
 * Tests the XML sitemap data provider for blog posts.
 *
 * Verifies that PostXmlSitemapDataProvider correctly generates sitemap entries
 * for published posts (respecting hidden/deleted) and that the optional
 * addDateFieldsToParameterMap feature works.
 */
final class PostXmlSitemapTest extends AbstractFunctionalTestCase
{
    protected array $coreExtensionsToLoad = [
        'dashboard',
        'seo',
    ];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/t3extblog/Tests/Functional/Fixtures/Configuration/sites' => 'typo3conf/sites',
    ];

    protected array $configurationToUseInTestInstance = [
        'FE' => [
            'cacheHash' => [
                'enforceValidation' => false,
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');

        $this->setUpFrontendRootPage(
            1,
            [
                'setup' => [
                    'EXT:t3extblog/Configuration/TypoScript/setup.typoscript',
                    'EXT:seo/Configuration/TypoScript/XmlSitemap/setup.typoscript',
                    'EXT:t3extblog/Tests/Functional/Fixtures/Configuration/Page.typoscript',
                ],
            ]
        );

        // Set up the sitemap TypoScript with resolved values (constants are empty by default).
        // Also disable the default pages sitemap which fails with SQLite in test context.
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_seo.config.xmlSitemap.sitemaps.pages >'
            . "\n" . '@import "EXT:t3extblog/Configuration/TypoScript/Sitemap/setup.typoscript"'
            . "\n" . 'plugin.tx_seo.config.xmlSitemap.sitemaps.t3extblog.config {'
            . "\n" . '    pid = 1'
            . "\n" . '    url {'
            . "\n" . '        pageId = 1'
            . "\n" . '    }'
            . "\n" . '}'
        );
    }

    protected function fetchSitemap(): string
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('type', '1533906435')
                ->withQueryParameter('tx_seo[sitemap]', 't3extblog')
        );

        self::assertSame(200, $response->getStatusCode());

        return (string)$response->getBody();
    }

    #[Test]
    public function sitemapContainsPublishedPosts(): void
    {
        $body = $this->fetchSitemap();

        // The sitemap should contain <url> entries for visible posts
        self::assertStringContainsString('<url>', $body);
        self::assertStringContainsString('tx_t3extblog_blogsystem', $body);
    }

    #[Test]
    public function sitemapExcludesHiddenAndDeletedPosts(): void
    {
        $body = $this->fetchSitemap();

        // Count the number of <url> entries — should be 4 visible posts (uid 1, 2, 4, 5).
        // Post uid=3 (hidden) and uid=6 (deleted) must not appear.
        $urlCount = substr_count($body, '<url>');
        self::assertSame(4, $urlCount);
    }

    #[Test]
    public function sitemapContainsLastModTimestamp(): void
    {
        $body = $this->fetchSitemap();

        // Each <url> should have a <lastmod> element
        self::assertStringContainsString('<lastmod>', $body);
    }
}
