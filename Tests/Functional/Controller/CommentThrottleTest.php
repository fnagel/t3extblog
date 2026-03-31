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

/**
 * Tests comment POST request throttling (rate limiting).
 *
 * The rate limiter is configured via TypoScript settings.blogsystem.comments.rateLimit
 * and uses a Symfony sliding window policy per IP address. The limiter is consumed
 * in initializeCreateAction() before the create action runs, so every POST counts
 * against the limit regardless of whether the spam check blocks the comment.
 */
final class CommentThrottleTest extends AbstractControllerTestCase
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

    /**
     * Build a comment POST request. Does NOT pass honeypot/human fields,
     * so spam check will block the comment — but rate limiter is consumed first.
     */
    protected function buildCommentRequest(string $suffix = ''): InternalRequest
    {
        $ns = 'tx_t3extblog_blogsystem';
        $trustedProperties = $this->buildTrustedPropertiesToken($ns, [
            "{$ns}[post]",
            "{$ns}[newComment][author]",
            "{$ns}[newComment][email]",
            "{$ns}[newComment][text]",
            "{$ns}[newComment][privacyPolicyAccepted]",
        ]);

        return (new InternalRequest())
            ->withMethod('POST')
            ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Comment')
            ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'create')
            ->withBody(Utils::streamFor(http_build_query([
                'tx_t3extblog_blogsystem' => [
                    '__trustedProperties' => $trustedProperties,
                    'post' => '1',
                    'newComment' => [
                        'author' => 'Throttle Tester ' . $suffix,
                        'email' => 'throttle' . $suffix . '@example.com',
                        'text' => 'Throttle test comment ' . $suffix,
                        'privacyPolicyAccepted' => '1',
                    ],
                ],
            ])));
    }

    #[Test]
    public function firstCommentSubmitIsNotRateLimited(): void
    {
        // With default limit=5, the first request is accepted.
        // The spam check may block the comment, but that's a separate concern —
        // we only verify the response does NOT contain the rate-limit flash message.
        $response = $this->executeFrontendSubRequest($this->buildCommentRequest('first'));
        $body = (string)$response->getBody();

        self::assertStringNotContainsString('rateLimit', $body);
    }

    #[Test]
    public function rateLimitBlocksAfterExceedingLimit(): void
    {
        // Override TypoScript: allow only 2 attempts per 30 minutes
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_t3extblog.settings.blogsystem.comments.rateLimit.limit = 2'
        );

        // First two requests: rate limiter accepts, spam check will handle them
        $response1 = $this->executeFrontendSubRequest($this->buildCommentRequest('a'));
        $body1 = (string)$response1->getBody();
        self::assertStringNotContainsString('rateLimit', $body1);

        $response2 = $this->executeFrontendSubRequest($this->buildCommentRequest('b'));
        $body2 = (string)$response2->getBody();
        self::assertStringNotContainsString('rateLimit', $body2);

        // Third request: rate limiter should reject (returns error action with flash message)
        $response3 = $this->executeFrontendSubRequest($this->buildCommentRequest('c'));
        // The error action renders HTML, not a redirect
        self::assertNotSame(303, $response3->getStatusCode());
    }

    #[Test]
    public function rateLimitIsSkippedWhenDisabled(): void
    {
        // Disable rate limiting but set limit=1
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_t3extblog.settings.blogsystem.comments.rateLimit.enable = 0'
            . "\n" . 'plugin.tx_t3extblog.settings.blogsystem.comments.rateLimit.limit = 1'
        );

        // Both requests should pass rate limiting (spam check may still block)
        $response1 = $this->executeFrontendSubRequest($this->buildCommentRequest('x'));
        $body1 = (string)$response1->getBody();
        self::assertStringNotContainsString('rateLimit', $body1);

        $response2 = $this->executeFrontendSubRequest($this->buildCommentRequest('y'));
        $body2 = (string)$response2->getBody();
        self::assertStringNotContainsString('rateLimit', $body2);
    }
}
