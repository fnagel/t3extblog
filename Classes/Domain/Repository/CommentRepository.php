<?php

namespace FelixNagel\T3extblog\Domain\Repository;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\Generic\Qom\AndInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\OrInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use FelixNagel\T3extblog\Domain\Model\Post;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * CommentRepository.
 */
class CommentRepository extends AbstractRepository
{
    protected $defaultOrderings = [
        'date' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * Finds all valid comments.
     */
    public function findValid(int $pid = null): QueryResultInterface
    {
        $query = $this->createQuery($pid);

        $query->matching(
            $this->getValidConstraints($query)
        );

        return $query->execute();
    }

    /**
     * Finds all comments for the given post.
     */
    public function findByPost(Post $post, bool $respectEnableFields = true): QueryResultInterface
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->equals('postId', $post->getUid());

        if (!$respectEnableFields) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
            $constraints[] = $query->equals('deleted', '0');
        }

        $query->matching(
            $query->logicalAnd($constraints)
        );

        return $query->execute();
    }

    /**
     * Finds all valid comments for the given post.
     */
    public function findValidByPost(Post $post): QueryResultInterface
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd([
                $this->getValidConstraints($query),
                $query->equals('postId', $post->getLocalizedUid()),
            ])
        );

        return $query->execute();
    }

    /**
     * Finds valid comments by email and post uid.
     */
    public function findValidByEmailAndPostId(string $email, int $postUid): QueryResultInterface
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd([
                $this->getFindByEmailAndPostIdConstraints($query, $email, $postUid),
                $this->getValidConstraints($query),
            ])
        );

        return $query->execute();
    }

    protected function createPendingQuery(int $pid = null, Post $post = null, int $limit = null): QueryInterface
    {
        $query = $this->createQuery($pid);

        if (is_int($limit) && $limit >= 1) {
            $query->setLimit($limit);
        }

        $constraints = [
            $this->getPendingConstraints($query)
        ];

        if ($post !== null) {
            $constraints[] = $query->equals('postId', $post->getUid());
        }

        $query->matching(
            $query->logicalAnd($constraints)
        );

        return $query;
    }

    /**
     * Finds pending comments by email and post uid.
     */
    public function findPendingByEmailAndPostId(string $email, int $postUid): QueryResultInterface
    {
        $query = $this->createPendingQuery();

        $query->matching(
            $query->logicalAnd($this->getFindByEmailAndPostIdConstraints($query, $email, $postUid))
        );

        return $query->execute();
    }

    /**
     * Finds pending comments by post.
     *
     * @todo Remove in version 8
     *
     * @deprecated
     */
    public function findPendingByPost(Post $post): QueryResultInterface
    {
        return $this->createPendingQuery(null, $post)->execute();
    }

    /**
     * Count pending comments by post.
     */
    public function countPendingByPost(Post $post): int
    {
        return $this->createPendingQuery(null, $post)->count();
    }

    /**
     * Finds all pending comments.
     *
     * @todo Remove in version 8
     *
     * @deprecated
     */
    public function findPending(): QueryResultInterface
    {
        return $this->createPendingQuery()->execute();
    }

    /**
     * Finds all pending comments by page.
     */
    public function findPendingByPage(int $pid = 0, int $limit = null): QueryResultInterface
    {
        return $this->createPendingQuery($pid, null, $limit)->execute();
    }

    /**
     * Count all pending comments by page.
     */
    public function countPendingByPage(int $pid = 0): int
    {
        return $this->createPendingQuery($pid)->count();
    }

    /**
     * Create constraints.
     */
    protected function getFindByEmailAndPostIdConstraints(QueryInterface $query, string $email, int $postUid): AndInterface
    {
        return $query->logicalAnd([
            $query->equals('email', $email),
            $query->equals('postId', $postUid),
        ]);
    }

    /**
     * Create constraints for valid comments.
     */
    protected function getValidConstraints(QueryInterface $query): AndInterface
    {
        return $query->logicalAnd([
            $query->equals('spam', 0),
            $query->equals('approved', 1),
        ]);
    }

    /**
     * Create constraints for pending comments.
     */
    protected function getPendingConstraints(QueryInterface $query): OrInterface
    {
        return $query->logicalOr([
            $query->equals('spam', 1),
            $query->equals('approved', 0),
        ]);
    }
}
