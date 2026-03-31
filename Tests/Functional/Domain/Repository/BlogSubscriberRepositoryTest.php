<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Domain\Repository;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\BlogSubscriber;
use FelixNagel\T3extblog\Domain\Repository\AbstractRepository;
use FelixNagel\T3extblog\Domain\Repository\AbstractSubscriberRepository;
use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;

#[CoversClass(BlogSubscriberRepository::class)]
#[CoversClass(AbstractSubscriberRepository::class)]
#[CoversClass(AbstractRepository::class)]
final class BlogSubscriberRepositoryTest extends AbstractRepositoryTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/blog_subscribers.csv');
    }

    protected function getRepository(): BlogSubscriberRepository
    {
        return $this->get(BlogSubscriberRepository::class);
    }

    // -------------------------------------------------------------------------
    // findForNotification
    // -------------------------------------------------------------------------

    #[Test]
    public function findForNotificationReturnsConfirmedSubscribers(): void
    {
        // uid=1 (Alice) and uid=4 (Diana) are confirmed (hidden=0).
        // uid=2 (Bob) and uid=3 (Charlie) are pending (hidden=1).
        $results = $this->getRepository()->findForNotification();

        self::assertCount(2, $results);
        $emails = array_map(fn(BlogSubscriber $s) => $s->getEmail(), iterator_to_array($results));
        self::assertContains('alice@example.com', $emails);
        self::assertContains('diana@example.com', $emails);
    }

    // -------------------------------------------------------------------------
    // findExistingSubscriptions
    // -------------------------------------------------------------------------

    #[Test]
    public function findExistingSubscriptionsFindsConfirmedSubscription(): void
    {
        // Alice (uid=1) is confirmed (hidden=0)
        $results = $this->getRepository()->findExistingSubscriptions('alice@example.com');

        self::assertCount(1, $results);
        self::assertSame(1, $results->getFirst()->getUid());
    }

    #[Test]
    public function findExistingSubscriptionsExcludesSpecifiedUid(): void
    {
        // Exclude Alice's own record
        $results = $this->getRepository()->findExistingSubscriptions('alice@example.com', 1);

        self::assertCount(0, $results);
    }

    #[Test]
    public function findExistingSubscriptionsReturnsEmptyForUnknownEmail(): void
    {
        $results = $this->getRepository()->findExistingSubscriptions('unknown@example.com');

        self::assertCount(0, $results);
    }

    // -------------------------------------------------------------------------
    // findByCode
    // -------------------------------------------------------------------------

    #[Test]
    public function findByCodeReturnsConfirmedSubscriberByCode(): void
    {
        $subscriber = $this->getRepository()->findByCode('alice_blog_code');

        self::assertInstanceOf(BlogSubscriber::class, $subscriber);
        self::assertSame('alice@example.com', $subscriber->getEmail());
    }

    #[Test]
    public function findByCodeReturnsNullForUnknownCode(): void
    {
        $subscriber = $this->getRepository()->findByCode('nonexistent_code');

        self::assertNull($subscriber);
    }

    // -------------------------------------------------------------------------
    // Lifecycle: pending subscriber becomes confirmed
    // -------------------------------------------------------------------------

    #[Test]
    public function subscriberLifecycleFromPendingToConfirmed(): void
    {
        // Initially Alice (uid=1) and Diana (uid=4) are confirmed
        self::assertCount(2, $this->getRepository()->findForNotification());

        // Bob confirms his subscription (opt-in link clicked)
        $this->get(ConnectionPool::class)
            ->getConnectionForTable('tx_t3blog_blog_nl')
            ->update('tx_t3blog_blog_nl', ['hidden' => 0, 'lastsent' => 1609545600], ['uid' => 2]);

        // Now Alice, Bob, and Diana are confirmed
        self::assertCount(3, $this->getRepository()->findForNotification());
    }
}
