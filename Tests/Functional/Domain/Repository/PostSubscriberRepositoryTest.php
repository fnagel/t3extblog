<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Domain\Repository;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;
use FelixNagel\T3extblog\Domain\Repository\AbstractSubscriberRepository;
use FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository;
use FelixNagel\T3extblog\Exception\Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;

#[CoversClass(PostSubscriberRepository::class)]
#[CoversClass(AbstractSubscriberRepository::class)]
final class PostSubscriberRepositoryTest extends AbstractRepositoryTestCase
{
    protected function makeComment(string $email, int $postId): Comment
    {
        $comment = new Comment();
        $comment->setEmail($email);
        $comment->setPostId($postId);
        return $comment;
    }

    protected function getRepository(): PostSubscriberRepository
    {
        return $this->get(PostSubscriberRepository::class);
    }

    // -------------------------------------------------------------------------
    // findForNotification
    // -------------------------------------------------------------------------

    #[Test]
    public function findForNotificationReturnsConfirmedSubscribersForPost(): void
    {
        $post = $this->getPost(1);

        // Only uid=1 (Alice) is confirmed (hidden=0) for post 1.
        // uid=2 (Bob) is pending (hidden=1), uid=3 (Charlie) is pending.
        $results = $this->getRepository()->findForNotification($post);

        self::assertCount(1, $results);
        self::assertSame('alice@example.com', $results->getFirst()->getEmail());
    }

    #[Test]
    public function findForNotificationReturnsEmptyForPostWithoutSubscribers(): void
    {
        // Post 3 (Draft Post) has no subscribers; fetch with enabled fields ignored since it's hidden
        $post3 = $this->getPost(3, false);
        $results = $this->getRepository()->findForNotification($post3);

        self::assertCount(0, $results);
    }

    // -------------------------------------------------------------------------
    // findExistingSubscriptions
    // -------------------------------------------------------------------------

    #[Test]
    public function findExistingSubscriptionsFindsConfirmedSubscription(): void
    {
        // Alice (uid=1) is confirmed (hidden=0) for post 1
        $results = $this->getRepository()->findExistingSubscriptions(1, 'alice@example.com');

        self::assertCount(1, $results);
        self::assertSame(1, $results->getFirst()->getUid());
    }

    #[Test]
    public function findExistingSubscriptionsExcludesSpecifiedUid(): void
    {
        // Exclude Alice's own record — used when updating an existing subscription
        $results = $this->getRepository()->findExistingSubscriptions(1, 'alice@example.com', 1);

        self::assertCount(0, $results);
    }

    #[Test]
    public function findExistingSubscriptionsReturnsEmptyForUnknownEmail(): void
    {
        $results = $this->getRepository()->findExistingSubscriptions(1, 'unknown@example.com');

        self::assertCount(0, $results);
    }

    // -------------------------------------------------------------------------
    // findForSubscriptionMail
    // -------------------------------------------------------------------------

    #[Test]
    public function findForSubscriptionMailFindsPendingOptInSubscriber(): void
    {
        $comment = $this->makeComment('bob@example.com', 1);
        // Bob (uid=2) is pending: hidden=1, lastSent=0
        $subscriber = $this->getRepository()->findForSubscriptionMail($comment);

        self::assertInstanceOf(PostSubscriber::class, $subscriber);
        self::assertSame('bob@example.com', $subscriber->getEmail());
    }

    #[Test]
    public function findForSubscriptionMailReturnsNullWhenOptInEmailAlreadySent(): void
    {
        // Charlie (uid=3) already has lastSent > 0 → opt-in email was already sent
        $comment = $this->makeComment('charlie@example.com', 1);
        $subscriber = $this->getRepository()->findForSubscriptionMail($comment);

        self::assertNull($subscriber);
    }

    #[Test]
    public function findForSubscriptionMailReturnsNullForConfirmedSubscriber(): void
    {
        // Alice (uid=1) is confirmed (hidden=0) → not in opt-in pending state
        $comment = $this->makeComment('alice@example.com', 1);
        $subscriber = $this->getRepository()->findForSubscriptionMail($comment);

        self::assertNull($subscriber);
    }

    #[Test]
    public function findForSubscriptionMailThrowsExceptionWhenEmailMissing(): void
    {
        $comment = new Comment();
        $comment->setEmail('');
        $comment->setPostId(1);

        // Empty email → exception expected
        $this->expectException(Exception::class);

        $this->getRepository()->findForSubscriptionMail($comment);
    }

    // -------------------------------------------------------------------------
    // findByCode
    // -------------------------------------------------------------------------

    #[Test]
    public function findByCodeReturnsConfirmedSubscriberByCode(): void
    {
        $subscriber = $this->getRepository()->findByCode('alice_code');

        self::assertInstanceOf(PostSubscriber::class, $subscriber);
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
        $post = $this->getPost(1);

        // Initially only Alice (uid=1) is confirmed for post 1
        self::assertCount(1, $this->getRepository()->findForNotification($post));

        // Bob confirms his subscription (opt-in link clicked)
        $this->get(ConnectionPool::class)
            ->getConnectionForTable('tx_t3blog_com_nl')
            ->update('tx_t3blog_com_nl', ['hidden' => 0, 'lastsent' => 1609545600], ['uid' => 2]);

        // Now both Alice and Bob are confirmed and will receive notifications
        self::assertCount(2, $this->getRepository()->findForNotification($post));
    }
}
