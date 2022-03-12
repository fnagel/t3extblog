<?php

namespace FelixNagel\T3extblog\UpgradeWizard;

use TYPO3\CMS\Install\Updates\ConfirmableInterface;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

abstract class AbstractMailsSentWizard extends AbstractUpgradeWizard implements ConfirmableInterface
{
    protected function updateRecordsForMailsSent($table, $field = 'mails_sent'): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);

        return $queryBuilder
            ->update($table)
            ->where(
                $queryBuilder->expr()->isNull($field)
            )
            ->set($field, 1)
            ->execute();
    }
}
