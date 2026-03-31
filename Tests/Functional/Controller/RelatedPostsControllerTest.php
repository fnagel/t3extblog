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

final class RelatedPostsControllerTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'RelatedPosts');
    }

    #[Test]
    public function relatedActionReturns200(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_relatedposts[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_relatedposts[action]', 'related')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function relatedActionRendersNoPostsWhenNotOnPostShowPage(): void
    {
        // The relatedAction checks PostController::isPostShowPage() — when accessed
        // without a blogsystem[post] routing argument the plugin outputs nothing.
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_relatedposts[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_relatedposts[action]', 'related')
        );

        // Plugin returns empty string; full HTML page wrapper is still present.
        self::assertSame(200, $response->getStatusCode());
        self::assertStringNotContainsString('First Post', (string)$response->getBody());
    }
}
