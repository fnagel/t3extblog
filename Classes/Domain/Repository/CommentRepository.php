<?php

namespace FelixNagel\T3extblog\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use FelixNagel\T3extblog\Domain\Model\Post;

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
     *
     * @param int $pid
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findValid($pid = null)
    {
        $query = $this->createQuery($pid);

        $query->matching(
            $this->getValidConstraints($query)
        );

        return $query->execute();
    }

    /**
     * Finds all comments for the given post.
     *
     * @param Post $post
     * @param bool $respectEnableFields
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByPost(Post $post, $respectEnableFields = true)
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->equals('postId', $post->getUid());

        if ($respectEnableFields === false) {
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
     *
     * @param Post $post
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findValidByPost(Post $post)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $this->getValidConstraints($query),
                $query->equals('postId', $post->getLocalizedUid())
            )
        );

        return $query->execute();
    }

    /**
     * Finds comments by email and post uid.
     *
     * @param string $email
     * @param int    $postUid
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByEmailAndPostId($email, $postUid)
    {
        $query = $this->createQuery();

        $query->matching(
            $this->getFindByEmailAndPostIdConstraints($query, $email, $postUid)
        );

        return $query->execute();
    }

    /**
     * Finds valid comments by email and post uid.
     *
     * @param string $email
     * @param int    $postUid
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findValidByEmailAndPostId($email, $postUid)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $this->getFindByEmailAndPostIdConstraints($query, $email, $postUid),
                $this->getValidConstraints($query)
            )
        );

        return $query->execute();
    }

    /**
     * Finds pending comments by email and post uid.
     *
     * @param string $email
     * @param int    $postUid
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findPendingByEmailAndPostId($email, $postUid)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $this->getFindByEmailAndPostIdConstraints($query, $email, $postUid),
                $this->getPendingConstraints($query)
            )
        );

        return $query->execute();
    }

    /**
     * Finds pending comments by post.
     *
     * @param Post $post
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findPendingByPost(Post $post)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd(
                $query->equals('postId', $post->getUid()),
                $this->getPendingConstraints($query)
            )
        );

        return $query->execute();
    }

    /**
     * Finds all pending comments.
     */
    public function findPending()
    {
        $query = $this->createQuery();

        $query->matching(
            $this->getPendingConstraints($query)
        );

        return $query->execute();
    }

    /**
     * Finds all pending comments by page.
     *
     * @param int $pid
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findPendingByPage($pid = 0)
    {
        $query = $this->createQuery((int) $pid);

        $query->matching(
            $this->getPendingConstraints($query)
        );

        return $query->execute();
    }

    /**
     * Create constraints.
     *
     * @param QueryInterface $query
     * @param string         $email
     * @param int            $postUid
     *
     * @return object
     */
    protected function getFindByEmailAndPostIdConstraints(QueryInterface $query, $email, $postUid)
    {
        $constraints = $query->logicalAnd(
            $query->equals('email', $email),
            $query->equals('postId', $postUid)
        );

        return $constraints;
    }

    /**
     * Create constraints for valid comments.
     *
     * @param Tx_Extbase_Persistence_QueryInterface $query
     *
     * @return object
     */
    protected function getValidConstraints(QueryInterface $query)
    {
        $constraints = $query->logicalAnd(
            $query->equals('spam', 0),
            $query->equals('approved', 1)
        );

        return $constraints;
    }

    /**
     * Create constraints for pending comments.
     *
     * @return object
     */
    protected function getPendingConstraints(QueryInterface $query)
    {
        $constraints = $query->logicalOr(
            $query->equals('spam', 1),
            $query->equals('approved', 0)
        );

        return $constraints;
    }
}
