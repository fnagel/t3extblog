<?php

namespace FelixNagel\T3extblog\Utility;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * BlogPageSearchUtility.
 */
class BlogPageSearchUtility implements SingletonInterface
{
    protected static ?array $cache = null;

    /**
     * The database connection.
     */
    protected static ?ConnectionPool $connectionPool = null;

    
    public static function getBlogPageUids(): array
    {
        return array_column(self::getBlogRelatedPages(), 'uid');
    }

    
    public static function getBlogRelatedPages(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $pages = array_merge_recursive(
            // Get pages with set module property
            self::getBlogModulePages(),
            // Search for blog related records
            self::getPagesWithBlogRecords('tx_t3blog_post'),
            self::getPagesWithBlogRecords('tx_t3blog_com'),
            self::getPagesWithBlogRecords('tx_t3blog_com_nl'),
            self::getPagesWithBlogRecords('tx_t3blog_blog_nl'),
        );

        self::$cache = array_unique($pages, SORT_REGULAR);

        return self::$cache;
    }

    /**
     * Run query for getting page info.
     *
     */
    protected static function getBlogModulePages(): array
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

    
    protected static function getPagesWithBlogRecords(string $joinTable): array
    {
        $table = 'pages';
        $queryBuilder = self::getDatabaseConnection()->getQueryBuilderForTable($table);
        $queryBuilder
            ->select($table . '.uid', $table . '.title')
            ->from($table)
            ->groupBy($table.'.uid');

        $queryBuilder->join(
            $table,
            $joinTable,
            $joinTable,
            $queryBuilder->expr()->eq(
                $joinTable . '.pid',
                $queryBuilder->quoteIdentifier($table . '.uid')
            )
        );

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * Get database connection.
     *
     */
    protected static function getDatabaseConnection(): ConnectionPool
    {
        if (self::$connectionPool === null) {
            self::$connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        }

        return self::$connectionPool;
    }
}
