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
use FelixNagel\T3extblog\Domain\Model\PostSubscriber;
use FelixNagel\T3extblog\Exception\Exception;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * PostSubscriberRepository.
 */
class PostSubscriberRepository extends AbstractSubscriberRepository
{
    public function findForNotification(Post $post): QueryResultInterface
    {
        $query = $this->createQuery();

        $query->matching(
            $query->equals('postUid', $post->getUid())
        );

        return $query->execute();
    }

    /**
     * Searchs for already registered subscriptions.
     */
    public function findExistingSubscriptions(int $postUid, string $email, int $excludeUid = null): QueryResultInterface
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
     */
    public function findForSubscriptionMail(Comment $comment): ?PostSubscriber
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
