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
use FelixNagel\T3extblog\Domain\Repository\AbstractRepository;
use FelixNagel\T3extblog\Domain\Repository\AbstractSubscriberRepository;
use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;

#[CoversClass(CommentRepository::class)]
#[CoversClass(AbstractSubscriberRepository::class)]
#[CoversClass(AbstractRepository::class)]
final class CommentRepositoryTest extends AbstractRepositoryTestCase
{
    protected function getRepository(): CommentRepository
    {
        return $this->get(CommentRepository::class);
    }

    // -------------------------------------------------------------------------
    // findValid
    // -------------------------------------------------------------------------

    #[Test]
    public function findValidReturnsOnlyApprovedNonSpamComments(): void
    {
        $results = $this->getRepository()->findValid(1);

        // Fixture: uid=1,4,5 are approved and non-spam. uid=7 is hidden.
        self::assertCount(3, $results);
        foreach ($results as $comment) {
            self::assertTrue($comment->isApproved());
            self::assertFalse($comment->isSpam());
        }
    }

    // -------------------------------------------------------------------------
    // findByPost
    // -------------------------------------------------------------------------

    #[Test]
    public function findByPostReturnsAllEnabledCommentsForPost(): void
    {
        $post = $this->getPost(1);
        // Post 1 has uid=1,2,3,5 enabled. uid=6 is deleted, uid=7 is hidden.
        $results = $this->getRepository()->findByPost($post);

        self::assertCount(4, $results);
    }

    #[Test]
    public function findByPostIncludesHiddenWhenEnableFieldsIgnored(): void
    {
        $post = $this->getPost(1);
        // With enable fields ignored, hidden uid=7 is included; deleted uid=6 is still excluded.
        $results = $this->getRepository()->findByPost($post, false);

        self::assertCount(5, $results);
    }

    // -------------------------------------------------------------------------
    // findValidByPost
    // -------------------------------------------------------------------------

    #[Test]
    public function findValidByPostReturnsOnlyValidCommentsForPost(): void
    {
        $post = $this->getPost(1);
        // Valid comments on post 1: uid=1 and uid=5 (approved, not spam, not hidden)
        $results = $this->getRepository()->findValidByPost($post);

        self::assertCount(2, $results);
        foreach ($results as $comment) {
            self::assertSame(1, $comment->getPostId());
            self::assertTrue($comment->isApproved());
            self::assertFalse($comment->isSpam());
        }
    }

    // -------------------------------------------------------------------------
    // findPendingByPage / countPendingByPost
    // -------------------------------------------------------------------------

    #[Test]
    public function findPendingByPageReturnsBothSpamAndUnapprovedComments(): void
    {
        $results = $this->getRepository()->findPendingByPage(1);

        // uid=2 (unapproved) and uid=3 (spam) are pending; hidden/deleted are excluded.
        self::assertCount(2, $results);
        $uids = array_map(fn(Comment $c) => $c->getUid(), iterator_to_array($results));
        self::assertContains(2, $uids);
        self::assertContains(3, $uids);
    }

    #[Test]
    public function countPendingByPostReturnsCorrectCount(): void
    {
        $post = $this->getPost(1);

        self::assertSame(2, $this->getRepository()->countPendingByPost($post));
    }

    // -------------------------------------------------------------------------
    // findValidByEmailAndPostId / findPendingByEmailAndPostId
    // -------------------------------------------------------------------------

    #[Test]
    public function findValidByEmailAndPostIdReturnsAllValidCommentsForThatAuthor(): void
    {
        // Alice has two valid comments (uid=1 and uid=5) on post 1
        $results = $this->getRepository()->findValidByEmailAndPostId('alice@example.com', 1);

        self::assertCount(2, $results);
    }

    #[Test]
    public function findValidByEmailAndPostIdReturnsEmptyForSpamAuthor(): void
    {
        // Charlie's comment is spam – should not appear in valid results
        $results = $this->getRepository()->findValidByEmailAndPostId('charlie@example.com', 1);

        self::assertCount(0, $results);
    }

    #[Test]
    public function findPendingByEmailAndPostIdReturnsPendingComment(): void
    {
        // Bob's comment is unapproved (pending)
        $results = $this->getRepository()->findPendingByEmailAndPostId('bob@example.com', 1);

        self::assertCount(1, $results);
    }

    // -------------------------------------------------------------------------
    // Lifecycle: spam comment approved by admin
    // -------------------------------------------------------------------------

    #[Test]
    public function spamCommentBecomesValidAfterAdminApproval(): void
    {
        $post = $this->getPost(1);

        // Before: uid=2 (unapproved) and uid=3 (spam) are both pending
        self::assertSame(2, $this->getRepository()->countPendingByPost($post));

        // Admin removes spam flag and approves comment uid=3
        $this->get(ConnectionPool::class)
            ->getConnectionForTable('tx_t3blog_com')
            ->update('tx_t3blog_com', ['spam' => 0, 'approved' => 1], ['uid' => 3]);

        // After: only uid=2 remains pending
        self::assertSame(1, $this->getRepository()->countPendingByPost($post));
    }
}
