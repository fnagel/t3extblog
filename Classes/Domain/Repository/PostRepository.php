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
     * Gets post by uid.
     *
     * Workaround as long as setRespectStoragePage does not work
     * See related bug: https://forge.typo3.org/issues/47192
     *
     * @todo This should be changed to a default findByUid when above bug is fixed
     *
     * @param int  $uid                 id of record
     * @param bool $respectEnableFields if set to false, hidden records are shown
     *
     * @return Post
     */
    public function findByLocalizedUid($uid, $respectEnableFields = true)
    {
        $temp = $GLOBALS['TCA']['tx_t3blog_post']['ctrl']['languageField'];
        $GLOBALS['TCA']['tx_t3blog_post']['ctrl']['languageField'] = null;

        $post = $this->findByUid($uid, $respectEnableFields);

        $GLOBALS['TCA']['tx_t3blog_post']['ctrl']['languageField'] = $temp;

        return $post;
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
            $query->like('tagCloud', '%'.$this->getDatabase()->escapeStrForLike($tag, 'tx_t3blog_post').'%')
        );

        return $query->execute();
    }

    /**
     * Returns all objects of this repository with matching category.
     *
     * @todo Rework this when extbase bug is fixed:
     * https://forge.typo3.org/issues/57272
     *
     * @param Category $category
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByCategory($category)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectSysLanguage(false);

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
