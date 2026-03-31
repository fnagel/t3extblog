<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Controller;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

final class BlogsystemControllerTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/comments.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/categories.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');

        $this->setUpFrontendWithTypoScript();
    }

    #[Test]
    public function listActionReturns200(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'list')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function listActionRendersPostTitles(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'list')
        );

        self::assertStringContainsString('First Post', (string)$response->getBody());
    }

    #[Test]
    public function listActionDoesNotRenderHiddenPosts(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'list')
        );

        self::assertStringNotContainsString('Draft Post', (string)$response->getBody());
    }

    #[Test]
    public function showActionReturns200ForExistingPost(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'show')
                ->withQueryParameter('tx_t3extblog_blogsystem[post]', '1')
        );

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('First Post', (string)$response->getBody());
    }

    #[Test]
    public function categoryActionReturns200(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'category')
                ->withQueryParameter('tx_t3extblog_blogsystem[category]', '1')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function tagActionReturns200ForKnownTag(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'tag')
                ->withQueryParameter('tx_t3extblog_blogsystem[tag]', 'php')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function authorActionReturns200(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'author')
                ->withQueryParameter('tx_t3extblog_blogsystem[author]', '1')
        );

        // author action returns 200 even when no posts found for that author uid
        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function permalinkActionRedirectsToShowAction(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'permalink')
                ->withQueryParameter('tx_t3extblog_blogsystem[permalinkPost]', '1')
        );

        self::assertSame(303, $response->getStatusCode());
    }

    #[Test]
    public function commentShowActionRedirectsToPostShowAction(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Comment')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'show')
                ->withQueryParameter('tx_t3extblog_blogsystem[post]', '1')
        );

        self::assertSame(303, $response->getStatusCode());
    }

    #[Test]
    public function commentCreateActionRedirectsAfterValidSubmit(): void
    {
        $ns = 'tx_t3extblog_blogsystem';
        $trustedProperties = $this->buildTrustedPropertiesToken($ns, [
            "{$ns}[post]",
            "{$ns}[newComment][author]",
            "{$ns}[newComment][email]",
            "{$ns}[newComment][text]",
            "{$ns}[newComment][privacyPolicyAccepted]",
        ]);

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withMethod('POST')
                ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Comment')
                ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'create')
                ->withBody(Utils::streamFor(http_build_query([
                    'tx_t3extblog_blogsystem' => [
                        '__trustedProperties' => $trustedProperties,
                        'post' => '1',
                        'newComment' => [
                            'author' => 'Test User',
                            'email' => 'test@example.com',
                            'text' => 'A test comment.',
                            'privacyPolicyAccepted' => '1',
                        ],
                    ],
                ])))
        );

        self::assertSame(303, $response->getStatusCode());
    }
}
