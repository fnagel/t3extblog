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
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

final class RssControllerTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'Rss');
        $this->addTypoScriptToTemplateRecord(1, "@import 'EXT:t3extblog/Configuration/TypoScript/Rss/setup.typoscript");
    }

    #[Test]
    public function rssActionReturns200(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_rss[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_rss[action]', 'rss')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function rssActionRendersXmlWithPostTitles(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_rss[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_rss[action]', 'rss')
        );

        $body = (string)$response->getBody();
        self::assertStringContainsString('First Post', $body);
        self::assertStringNotContainsString('Draft Post', $body);
    }

    #[Test]
    public function rssActionReturnsValidXml(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_rss[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_rss[action]', 'rss')
        );

        $body = (string)$response->getBody();

        $previousUseErrors = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($body);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previousUseErrors);

        self::assertNotFalse($xml, 'RSS output is not valid XML: ' . ($errors[0]->message ?? 'unknown error'));
        self::assertSame('rss', $xml->getName());
        self::assertNotEmpty($xml->channel->item, 'RSS feed contains no items');
    }
}
