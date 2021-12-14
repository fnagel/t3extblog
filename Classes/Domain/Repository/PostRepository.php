<?php

namespace FelixNagel\T3extblog\Domain\Repository;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use FelixNagel\T3extblog\Domain\Model\BackendUser;
use FelixNagel\T3extblog\Domain\Model\Category;
use FelixNagel\T3extblog\Domain\Model\Post;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * PostRepository.
 */
class PostRepository extends AbstractRepository
{
    protected $defaultOrderings = [
        'publishDate' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * Override default findByUid function to enable also the option to turn of
     * the enableField setting.
     *
     */
    public function findByUid($uid, bool $respectEnableFields = true): Post
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->getQuerySettings()->setIgnoreEnableFields(!$respectEnableFields);

        $query->matching(
            $query->logicalAnd([
                $query->equals('uid', $uid),
                $query->equals('deleted', 0),
            ])
        );

        return $query->execute()->getFirst();
    }

    /**
     * Gets localized post by uid. No overlay.
     */
    public function findByLocalizedUid(int $uid, bool $respectEnableFields = true): Post
    {
        return  $this->findByUid($uid, $respectEnableFields);
    }

    /**
     * Get next post.
     */
    public function nextPost(Post $post): ?Post
    {
        $query = $this->createQuery();

        $query->setOrderings(
            ['publishDate' => QueryInterface::ORDER_ASCENDING]
        );

        $query->matching($query->greaterThan('publishDate', $post->getPublishDate()));

        return $query->execute()->getFirst();
    }

    /**
     * Get previous post.
     */
    public function previousPost(Post $post): ?Post
    {
        $query = $this->createQuery();

        $query->matching($query->lessThan('publishDate', $post->getPublishDate()));

        return $query->execute()->getFirst();
    }

    /**
     * Find all or filtered by tag, category or author.
     */
    public function findByFilter($filter = null): ?QueryResultInterface
    {
        if ($filter === null) {
            return $this->findAll();
        }

        if ($filter instanceof BackendUser) {
            return $this->findByAuthor($filter);
        }

        if ($filter instanceof Category) {
            return $this->findByCategory($filter);
        }

        if (is_string($filter)) {
            return $this->findByTag($filter);
        }

        return null;
    }

    /**
     * Finds posts by the specified tag.
     */
    public function findByTag(string $tag): QueryResultInterface
    {
        $query = $this->createQuery();

        $query->matching(
            $query->like('tagCloud', '%'.$this->escapeStrForLike($tag).'%')
        );

        return $query->execute();
    }

    /**
     * Returns all objects of this repository with matching category.
     */
    public function findByCategory(Category $category): QueryResultInterface
    {
        $query = $this->createQuery();

        $constraints = [];
        $constraints[] = $query->contains('categories', $category);

        $categories = $category->getChildCategories();

        if (!is_null($categories) && count($categories) > 0) {
            foreach ($categories as $childCategory) {
                $constraints[] = $query->contains('categories', $childCategory);
            }
        }

        $query->matching($query->logicalOr($constraints));

        return $query->execute();
    }

    /**
     * Returns all hidden posts of a time frame from now.
     */
    public function findDrafts(int $pid = 0, int $limit = null, string $until = '-12 months'): QueryResultInterface
    {
        $query = $this->createQuery($pid);
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        if (is_int($limit) && $limit >= 1) {
            $query->setLimit($limit);
        }

        $date = new \DateTime();
        $date->modify($until);

        $query->matching(
            $query->logicalAnd([
                $query->equals('hidden', 1),
                $query->equals('deleted', 0),
                $query->greaterThanOrEqual('crdate', $date->getTimestamp()),
            ])
        );

        return $query->execute();
    }
}
