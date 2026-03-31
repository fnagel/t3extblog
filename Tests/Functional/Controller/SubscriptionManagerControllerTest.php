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

final class SubscriptionManagerControllerTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/post_subscribers.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'SubscriptionManager');
    }

    #[Test]
    public function subscriberListActionReturns400WithoutValidAuth(): void
    {
        // Without a valid auth code in the session, the action forwards to the error action.
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[controller]', 'Subscriber')
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[action]', 'list')
        );

        self::assertSame(400, $response->getStatusCode());
    }

    #[Test]
    public function subscriberLogoutActionReturns400(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[controller]', 'Subscriber')
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[action]', 'logout')
        );

        self::assertSame(400, $response->getStatusCode());
    }

    #[Test]
    public function postSubscriberListActionReturns400WithoutValidAuth(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[controller]', 'PostSubscriber')
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[action]', 'list')
        );

        self::assertSame(400, $response->getStatusCode());
    }

    #[Test]
    public function postSubscriberConfirmActionWithInvalidCodeReturns400(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[controller]', 'PostSubscriber')
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[action]', 'confirm')
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[code]', 'invalidcode')
        );

        self::assertSame(400, $response->getStatusCode());
    }

    #[Test]
    public function postSubscriberDeleteActionWithInvalidCodeReturns400(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[controller]', 'PostSubscriber')
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[action]', 'delete')
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[code]', 'invalidcode')
        );

        self::assertSame(400, $response->getStatusCode());
    }

    #[Test]
    public function blogSubscriberListActionReturns400WithoutValidAuth(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[controller]', 'BlogSubscriber')
                ->withQueryParameter('tx_t3extblog_subscriptionmanager[action]', 'list')
        );

        self::assertSame(400, $response->getStatusCode());
    }
}
