<?php

namespace FelixNagel\T3extblog\Domain\Repository;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     */
    public function findByUid($uid, bool $respectEnableFields = true): ?Post
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
     */
    public function findByLocalizedUid(int $uid, bool $respectEnableFields = true): ?Post
    {
        $table = $this->getTableName();
        $queryBuilder = $this->getQueryBuilder($table);

        if (!$respectEnableFields) {
            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        }

        $queryBuilder
            ->select('post.*')
            ->from($table, 'post')
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, ParameterType::INTEGER)
                ),
            )
            ->setMaxResults(1)
        ;

        return $this->createQuery()->statement($queryBuilder)->execute()->getFirst();
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
     * Get related posts.
     */
    public function relatedPosts(Post $post): ?QueryResultInterface
    {
        $query = $this->createQuery();

        $constraints = [];
        foreach ($post->getTagCloud() as $tag) {
            $constraints[] = $query->like('tagCloud', '%'.$this->escapeStrForLike($tag).'%');
        }

        $query->matching($query->logicalAnd(
            $query->logicalNot($query->equals('uid', $post->getUid())),
            $query->logicalOr(...$constraints)
        ));

        return $query->execute();
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
            return $this->findBy(['author' => $filter]);
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

        $query->matching($query->logicalOr(...$constraints));

        return $query->execute();
    }

    /**
     * Returns all hidden posts of a time frame from now.
     */
    public function findDrafts(int $pid = 0, ?int $limit = null, string $until = '-12 months'): QueryResultInterface
    {
        $query = $this->createQuery($pid);
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        if (is_int($limit) && $limit >= 1) {
            $query->setLimit($limit);
        }

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

    /**
     * Get tag cloud.
     *
     * @return null|array{tag: string, posts: int}[]
     */
    public function tagCloud(int $limit = 10, int $minimum = 2): ?array
    {
        $table = $this->getTableName();
        $connection = $this->getConnection($table);
        $query = 'WITH RECURSIVE all_tags AS (
            SELECT uid, TRIM(SUBSTRING_INDEX(tagClouds, ",", 1)) AS tag,
            SUBSTRING(tagClouds, LENGTH(SUBSTRING_INDEX(tagClouds, ",", 1)) + 2) AS rest
            FROM '.$table.'
            WHERE tagClouds IS NOT NULL AND tagClouds <> "" AND '.$this->getFeEnableFields($table).'

            UNION ALL

            SELECT uid, TRIM(SUBSTRING_INDEX(rest, ",", 1)) AS tag,
            CASE
                WHEN rest LIKE "%,%" THEN SUBSTRING(rest, LENGTH(SUBSTRING_INDEX(rest, ",", 1)) + 2)
            ELSE ""
            END AS rest
            FROM all_tags
            WHERE rest <> ""
        )
        SELECT tag as title, COUNT(*) AS posts
        FROM all_tags
        GROUP BY tag
        HAVING posts >= :minimum
        ORDER BY posts DESC
        LIMIT :limit';

        $params = [
            'limit' => $limit,
            'minimum' => $minimum,
        ];

        $query = $connection->executeQuery($query, $params);
        $result = $query->fetchAllAssociative() ?: null;
        $query->free();

        return $result;
    }
}
