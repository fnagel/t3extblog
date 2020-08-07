<?php

namespace FelixNagel\T3extblog\Domain\Repository;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Exception\Exception;

/**
 * PostSubscriberRepository.
 */
class PostSubscriberRepository extends AbstractSubscriberRepository
{
    /**
     * @param Post $post The post the comment is related to
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findForNotification(Post $post)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->equals('postUid', $post->getUid())
        );

        return $query->execute();
    }

    /**
     * Searchs for already registered subscriptions.
     *
     * @param int    $postUid
     * @param string $email
     * @param int    $excludeUid
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findExistingSubscriptions($postUid, $email, $excludeUid = null)
    {
        $query = $this->createQuery();

        $constraints = $this->getBasicExistingSubscriptionConstraints($query, $email, $excludeUid);
        $constraints[] = $query->equals('postUid', $postUid);

        $query->matching(
            $query->logicalAnd($constraints)
        );

        return $query->execute();
    }

    /**
     * Finds a single subscriber without opt-in mail sent before.
     *
     * @param Comment $comment
     *
     * @return object
     */
    public function findForSubscriptionMail(Comment $comment)
    {
        if (empty($comment->getEmail())) {
            throw new Exception('Email address is a required property!', 1592248975);
        }

        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        $constraints = [
            $query->equals('postUid', $comment->getPostId()),
            $query->equals('email', $comment->getEmail()),
            $query->equals('lastSent', 0),
            $query->equals('hidden', 1),
            $query->equals('deleted', 0),
        ];

        $query->matching(
            $query->logicalAnd($constraints)
        );

        return $query->execute()->getFirst();
    }
}
