<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Controller;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ResponseInterface;
use Doctrine\DBAL\ParameterType;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

/**
 * Functional tests for the BlogSubscriberController (SubscriptionManager plugin).
 *
 * Focuses on the confirmation flow of a pending blog subscription, which is not
 * covered elsewhere: the happy path (a valid code activates the subscriber) and
 * its negative counterpart (a valid-format but unknown code must not activate
 * anyone). The generic "no/invalid auth returns 400" paths are already covered by
 * {@see SubscriptionManagerControllerTest}.
 */
final class BlogSubscriberControllerTest extends AbstractControllerTestCase
{
    protected const PLUGIN = 'tx_t3extblog_subscriptionmanager';

    /**
     * Matches the code column in blog_subscribers_confirm.csv (uid 10, hidden).
     */
    protected const CONFIRM_CODE = 'abcdef0123456789abcdef0123456789';

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/blog_subscribers_confirm.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'SubscriptionManager');

        // The auth code expiry is relative to the subscriber's "last sent" date;
        // widen it so the fixture timestamp never counts as outdated.
        $this->addTypoScriptToTemplateRecord(
            1,
            'plugin.tx_t3extblog.settings.subscriptionManager.blog.subscriber.emailHashTimeout = +100 years'
        );
    }

    #[Test]
    public function confirmActionConfirmsPendingSubscriber(): void
    {
        self::assertSame(1, $this->fetchSubscriberHidden(10), 'Fixture subscriber should start hidden.');

        $response = $this->requestConfirm(self::CONFIRM_CODE);

        // A successful confirmation redirects to the subscriber list.
        self::assertSame(303, $response->getStatusCode());
        // The previously hidden subscriber is now visible (confirmed).
        self::assertSame(0, $this->fetchSubscriberHidden(10), 'Subscriber was not confirmed (still hidden).');
    }

    #[Test]
    public function confirmActionWithUnknownCodeDoesNotConfirm(): void
    {
        $response = $this->requestConfirm('ffffffffffffffffffffffffffffffff');

        // An unknown code fails authentication: the error page (HTTP 400) is shown.
        self::assertSame(400, $response->getStatusCode());
        // The subscriber must remain hidden (unconfirmed).
        self::assertSame(1, $this->fetchSubscriberHidden(10), 'Subscriber must not be confirmed with an unknown code.');
    }

    protected function requestConfirm(string $code): ResponseInterface
    {
        return $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter(self::PLUGIN . '[controller]', 'BlogSubscriber')
                ->withQueryParameter(self::PLUGIN . '[action]', 'confirm')
                ->withQueryParameter(self::PLUGIN . '[code]', $code)
        );
    }

    protected function fetchSubscriberHidden(int $uid): int
    {
        $queryBuilder = $this->getConnectionPool()->getQueryBuilderForTable('tx_t3blog_blog_nl');
        // Read the raw row regardless of hidden/deleted state.
        $queryBuilder->getRestrictions()->removeAll();

        $hidden = $queryBuilder
            ->select('hidden')
            ->from('tx_t3blog_blog_nl')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, ParameterType::INTEGER)))
            ->executeQuery()
            ->fetchOne();

        self::assertNotFalse($hidden, 'Expected subscriber row uid=' . $uid . ' to exist.');

        return (int)$hidden;
    }
}
