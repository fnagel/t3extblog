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
     * @param int  $uid                 id of record
     * @param bool $respectEnableFields if set to false, hidden records are shown
     *
     * @return Post
     */
    public function findByUid($uid, $respectEnableFields = true)
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->getQuerySettings()->setIgnoreEnableFields(!$respectEnableFields);

        $query->matching(
            $query->logicalAnd(
                $query->equals('uid', $uid),
                $query->equals('deleted', 0)
            )
        );

        return $query->execute()->getFirst();
    }

    /**
     * Gets localized post by uid. No overlay.
     *
     * @param int  $uid                 id of record
     * @param bool $respectEnableFields if set to false, hidden records are shown
     *
     * @return Post
     */
    public function findByLocalizedUid($uid, $respectEnableFields = true)
    {
        return  $this->findByUid($uid, $respectEnableFields);
    }

    /**
     * Get next post.
     *
     * @param Post $post
     *
     * @return Post
     */
    public function nextPost(Post $post)
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
     *
     * @param Post $post
     *
     * @return Post
     */
    public function previousPost(Post $post)
    {
        $query = $this->createQuery();

        $query->matching($query->lessThan('publishDate', $post->getPublishDate()));

        return $query->execute()->getFirst();
    }

    /**
     * Find all or filtered by tag, category or author.
     *
     * @param mixed $filter
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByFilter($filter = null)
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

        return;
    }

    /**
     * Finds posts by the specified tag.
     *
     * @param string $tag
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByTag($tag)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->like('tagCloud', '%'.$this->escapeStrForLike($tag).'%')
        );

        return $query->execute();
    }

    /**
     * Returns all objects of this repository with matching category.
     *
     * @param Category $category
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByCategory($category)
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
     *
     * @param int    $pid
     * @param string $until
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findDrafts($pid = 0, $until = '- 3 months')
    {
        $query = $this->createQuery((int) $pid);
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        $date = new \DateTime();
        $date->modify($until);

        $query->matching(
            $query->logicalAnd(
                $query->equals('hidden', 1),
                $query->equals('deleted', 0),
                $query->greaterThanOrEqual('crdate', $date->getTimestamp())
            )
        );

        return $query->execute();
    }
}
