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

final class ArchiveControllerTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'Archive');
    }

    #[Test]
    public function archiveActionReturns200(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_archive[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_archive[action]', 'archive')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function archiveActionRendersPostTitlesForYear(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_archive[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_archive[action]', 'archive')
                ->withQueryParameter('tx_t3extblog_archive[year]', '2025')
        );

        self::assertStringContainsString('First Post', (string)$response->getBody());
    }
}
