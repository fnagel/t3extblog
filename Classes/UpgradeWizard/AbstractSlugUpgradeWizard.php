<?php

namespace FelixNagel\T3extblog\UpgradeWizard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ConfirmableInterface;
use TYPO3\CMS\Install\Updates\RepeatableInterface;

abstract class AbstractSlugUpgradeWizard extends AbstractUpgradeWizard implements ConfirmableInterface, RepeatableInterface
{
    protected function countMissingSlugs(string $table = 'tx_t3blog_post', string $slug = 'url_segment'): int
    {
        $queryBuilder = $this->getSlugQueryBuilder($table);
        $constraint = $queryBuilder->expr()->eq($slug, $queryBuilder->createNamedParameter(''));

        return $queryBuilder
            ->select('*')
            ->from($table)
            ->where($constraint)
            ->execute()
            ->rowCount();
    }

    protected function createMissingSlugs(
        string $table = 'tx_t3blog_post',
        string $slug = 'url_segment',
        int $limit = 50
    ): int {
        $queryBuilder = $this->getSlugQueryBuilder($table);
        $constraint = $queryBuilder->expr()->eq($slug, $queryBuilder->createNamedParameter(''));
        $rows = $queryBuilder
            ->select('*')
            ->from($table)
            ->where($constraint)
            ->setMaxResults($limit)
            ->execute()
            ->fetchAll();

        if (count($rows) === 0) {
            return 0;
        }

        $fieldConfig = $GLOBALS['TCA'][$table]['columns'][$slug]['config'];
        $slugService = GeneralUtility::makeInstance(SlugHelper::class, $table, $slug, $fieldConfig);

        foreach ($rows as $row) {
            $queryBuilder
                ->update($table)->where([$constraint, $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($row['uid'], \PDO::PARAM_INT))])
                ->set($slug, $slugService->generate($row, $row['pid']))
                ->execute();
        }

        return count($rows);
    }

    protected function getSlugQueryBuilder(string $table = 'tx_t3blog_post'): QueryBuilder
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();

        return $queryBuilder;
    }
}
