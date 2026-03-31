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

final class LatestPostsControllerTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'LatestPosts');
    }

    #[Test]
    public function latestActionReturns200(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_latestposts[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_latestposts[action]', 'latest')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function latestActionRendersRecentPosts(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_latestposts[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_latestposts[action]', 'latest')
        );

        self::assertStringContainsString('First Post', (string)$response->getBody());
    }

    #[Test]
    public function latestActionDoesNotRenderHiddenPosts(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_latestposts[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_latestposts[action]', 'latest')
        );

        self::assertStringNotContainsString('Draft Post', (string)$response->getBody());
    }
}
