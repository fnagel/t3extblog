<?php

namespace FelixNagel\T3extblog\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2018 Felix Nagel <info@felixnagel.com>
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
