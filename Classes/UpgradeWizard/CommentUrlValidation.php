<?php

namespace FelixNagel\T3extblog\UpgradeWizard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class CommentUrlValidation extends AbstractManualCheckWizard
{
    public function getTitle(): string
    {
        return 'T3extblog: Find existing comments with invalid website URLs';
    }

    public function executeUpdate(): bool
    {
        return $this->findCommentRecordsForUrlValidation();
    }

    protected function findCommentRecordsForUrlValidation(): bool
    {
        $table = 'tx_t3blog_com';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->neq('website', $queryBuilder->createNamedParameter('')),
                $queryBuilder->expr()->notLike('website', $queryBuilder->createNamedParameter('http%')),
                $queryBuilder->expr()->notLike('website', $queryBuilder->createNamedParameter('https%'))
            );

        $rows = $queryBuilder->execute()->fetchAll();

        if (count($rows) === 0) {
            $this->output->writeln('All comment URLs look valid. Good job!');
            return true;
        }

        $message = count($rows) . ' comments with invalid website URLs have been found.';
        $message .= ' Make sure to remove or fix those URLs!';
        $this->output->writeln($message);

        $commentList = array_map(fn($comment) => $comment['uid'], $rows);
        $this->output->writeln('List of comment UIDs: '.implode(', ', $commentList));

        return false;
    }
}
