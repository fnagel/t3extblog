<?php

namespace FelixNagel\T3extblog\Utility;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * BlogPageSearchUtility.
 */
class BlogPageSearchUtility
{
    /**
     * The database connection.
     *
     * @var ConnectionPool
     */
    protected static $connectionPool;

    /**
     * @return array
     */
    public static function getBlogRelatedPages()
    {
        $pages = array_merge_recursive(
            // Get pages with set module property
            self::getBlogModulePages(),
            // Split the join queries because otherwise the query is awful slow
            self::getPagesWithBlogRecords(['tx_t3blog_post', 'tx_t3blog_com']),
            self::getPagesWithBlogRecords(['tx_t3blog_com_nl', 'tx_t3blog_blog_nl'])
        );

        return array_unique($pages, SORT_REGULAR);
    }

    /**
     * Run query for getting page info.
     *
     * @return array
     */
    protected static function getBlogModulePages()
    {
        $table = 'pages';
        $queryBuilder = self::getDatabaseConnection()->getQueryBuilderForTable($table);
        $queryBuilder
            ->select('uid', 'title')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('module', $queryBuilder->createNamedParameter('t3blog')),
                $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
            );

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * @param $joinTables
     *
     * @return array
     */
    protected static function getPagesWithBlogRecords($joinTables)
    {
        $table = 'pages';
        $queryBuilder = self::getDatabaseConnection()->getQueryBuilderForTable($table);
        $queryBuilder
            ->select($table . '.uid', $table . '.title')
            ->from($table)
            ->groupBy($table.'.uid');

        foreach ($joinTables as $joinTable) {
            $queryBuilder->leftJoin(
                $table,
                $joinTable,
                $joinTable,
                $queryBuilder->expr()->eq(
                    $table . '.uid',
                    $queryBuilder->quoteIdentifier($joinTable . '.pid')
                )
            );
        }

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Get database connection.
     *
     * @return ConnectionPool
     */
    protected static function getDatabaseConnection()
    {
        if (self::$connectionPool === null) {
            self::$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        }

        return self::$connectionPool;
    }
}
