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

/**
 * AbstractRepository.
 */
class AbstractRepository extends Repository
{
    /**
     * @param null $pageUid
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
     */
    public function createQuery($pageUid = null)
    {
        $query = parent::createQuery();

        if ($pageUid !== null) {
            $query->getQuerySettings()->setStoragePageIds([(int) $pageUid]);
        }

        return $query;
    }

    /**
     * Returns all objects with specific PID.
     *
     * @param int  $pid
     * @param bool $respectEnableFields
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByPage($pid = null, $respectEnableFields = true)
    {
        $query = $this->createQuery($pid);

        if ($respectEnableFields === false) {
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

        $queryBuilder = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Database\ConnectionPool::class
            )
            ->getQueryBuilderForTable($table);

        return $queryBuilder->escapeLikeWildcards($value);
    }
}
