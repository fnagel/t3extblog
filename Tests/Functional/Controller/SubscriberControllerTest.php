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

/**
 * Functional tests for the SubscriberController (SubscriptionManager plugin).
 *
 * Covers the authenticated "my subscriptions" list: a confirmed subscriber opens
 * a link containing the auth code, which logs the session in and forwards to the
 * aggregated subscription list. The session cookie from that first request is
 * reused to render SubscriberController::list for the logged-in email.
 */
final class SubscriberControllerTest extends AbstractControllerTestCase
{
    protected const PLUGIN = 'tx_t3extblog_subscriptionmanager';

    /**
     * Matches the code column in post_subscribers_auth.csv (uid 20, confirmed).
     */
    protected const AUTH_CODE = 'fedcba9876543210fedcba9876543210';

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/post_subscribers_auth.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'SubscriptionManager');
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_t3extblog.settings.subscriptionManager.comment.subscriber.emailHashTimeout = +100 years'
        );
    }

    #[Test]
    public function authenticatedSubscriberSeesOwnSubscriptionList(): void
    {
        // Step 1: open the code link. This logs the session in and redirects to the
        // aggregated subscriber list, handing out a frontend session cookie.
        $authResponse = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter(self::PLUGIN . '[controller]', 'PostSubscriber')
                ->withQueryParameter(self::PLUGIN . '[action]', 'list')
                ->withQueryParameter(self::PLUGIN . '[code]', self::AUTH_CODE)
        );
        self::assertSame(303, $authResponse->getStatusCode(), 'Valid code did not authenticate the subscriber.');

        $sessionId = $this->extractSessionCookie($authResponse);
        self::assertNotSame('', $sessionId, 'No frontend session cookie was issued on login.');

        // Step 2: render the subscriber list reusing the authenticated session.
        $listResponse = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter(self::PLUGIN . '[controller]', 'Subscriber')
                ->withQueryParameter(self::PLUGIN . '[action]', 'list')
                ->withCookieParams(['fe_typo_user' => $sessionId])
        );

        self::assertSame(200, $listResponse->getStatusCode());
        // The list is rendered for the authenticated email and shows its subscription.
        self::assertStringContainsString('member@example.com', (string)$listResponse->getBody());
    }

    #[Test]
    public function listActionWithoutSessionDoesNotRenderList(): void
    {
        // Without an authenticated session the aggregated list must not be shown.
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter(self::PLUGIN . '[controller]', 'Subscriber')
                ->withQueryParameter(self::PLUGIN . '[action]', 'list')
        );

        self::assertStringNotContainsString('member@example.com', (string)$response->getBody());
    }

    protected function extractSessionCookie(\Psr\Http\Message\ResponseInterface $response): string
    {
        foreach ($response->getHeader('Set-Cookie') as $setCookie) {
            if (str_starts_with($setCookie, 'fe_typo_user=')) {
                // Return the bare cookie value (strip name and attributes).
                $nameValue = explode(';', $setCookie, 2)[0];

                return substr($nameValue, strlen('fe_typo_user='));
            }
        }

        return '';
    }
}
