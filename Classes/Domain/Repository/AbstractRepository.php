<?php

namespace FelixNagel\T3extblog\Domain\Repository;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * AbstractRepository.
 */
abstract class AbstractRepository extends Repository
{
    public function createQuery(int $pageUid = null): QueryInterface
    {
        $query = parent::createQuery();

        if ($pageUid !== null) {
            if ($pageUid >= 0) {
                $query->getQuerySettings()->setStoragePageIds([$pageUid]);
            } else {
                $query->getQuerySettings()->setRespectStoragePage(false);
            }
        }

        return $query;
    }

    /**
     * Returns all objects with specific PID.
     */
    public function findByPage(int $pid = null, bool $respectEnableFields = true, int $limit = null): QueryResultInterface
    {
        $query = $this->createQuery($pid);

        if (is_int($limit) && $limit >= 1) {
            $query->setLimit($limit);
        }

        if (!$respectEnableFields) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);

            $query->matching(
                $query->equals('deleted', '0')
            );
        }

        return $query->execute();
    }

    protected function getTableName(QueryInterface $query = null): string
    {
        if ($query === null) {
            $query = $this->createQuery();
        }

        return $this->getTableNameByClass($query->getType());
    }

    protected function getTableNameByClass(string $object): string
    {
        return $this->objectManager->get(DataMapper::class)->convertClassNameToTableName($object);
    }

    protected function escapeStrForLike(string $value, string $table = null): string
    {
        if ($table === null) {
            $table = $this->getTableName();
        }

        $queryBuilder = GeneralUtility::makeInstance(
            ConnectionPool::class
        )
            ->getQueryBuilderForTable($table);

        return $queryBuilder->escapeLikeWildcards($value);
    }
}
