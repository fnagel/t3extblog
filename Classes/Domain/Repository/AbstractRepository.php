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
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * AbstractRepository.
 */
abstract class AbstractRepository extends Repository
{
    /**
     * @param int $pageUid
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    public function createQuery($pageUid = null)
    {
        $query = parent::createQuery();

        if ($pageUid !== null) {
            $pageUid = (int) $pageUid;

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
     *
     * @param int  $pid
     * @param bool $respectEnableFields
     * @param int  $limit
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByPage($pid = null, $respectEnableFields = true, $limit = null)
    {
        $query = $this->createQuery($pid);

        if (is_int($limit)) {
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

    /**
     * @param QueryInterface|null $query
     * @return string
     */
    protected function getTableName(QueryInterface $query = null)
    {
        if (empty($query)) {
            $query = $this->createQuery();
        }

        return $this->getTableNameByClass($query->getType());
    }

    /**
     * @param string $object
     * @return string
     */
    protected function getTableNameByClass($object)
    {
        return $this->objectManager->get(DataMapper::class)->convertClassNameToTableName($object);
    }

    /**
     * @param string $value
     * @param string $table
     * @return string
     */
    protected function escapeStrForLike($value, $table = null)
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
