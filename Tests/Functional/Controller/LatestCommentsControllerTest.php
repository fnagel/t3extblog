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

final class LatestCommentsControllerTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/comments.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'LatestComments');
    }

    #[Test]
    public function latestActionReturns200(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_latestcomments[controller]', 'Comment')
                ->withQueryParameter('tx_t3extblog_latestcomments[action]', 'latest')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function latestActionRendersApprovedComments(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_latestcomments[controller]', 'Comment')
                ->withQueryParameter('tx_t3extblog_latestcomments[action]', 'latest')
        );

        self::assertStringContainsString('Great post!', (string)$response->getBody());
    }

    #[Test]
    public function latestActionDoesNotRenderUnapprovedComments(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_latestcomments[controller]', 'Comment')
                ->withQueryParameter('tx_t3extblog_latestcomments[action]', 'latest')
        );

        // uid=2 is unapproved, uid=3 is spam
        self::assertStringNotContainsString('Waiting for approval', (string)$response->getBody());
        self::assertStringNotContainsString('Buy cheap watches', (string)$response->getBody());
    }
}
