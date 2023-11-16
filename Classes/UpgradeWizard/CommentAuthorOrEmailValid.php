<?php

namespace FelixNagel\T3extblog\UpgradeWizard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Install\Attribute\UpgradeWizard;

#[UpgradeWizard(CommentAuthorOrEmailValid::class)]
class CommentAuthorOrEmailValid extends AbstractManualCheckWizard
{
    public function getTitle(): string
    {
        return 'T3extblog: Find existing comments with invalid author or email';
    }

    public function executeUpdate(): bool
    {
        return $this->findCommentAuthorOrEmailInvalid();
    }

    protected function findCommentAuthorOrEmailInvalid(): bool
    {
        $table = 'tx_t3blog_com';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->eq('author', $queryBuilder->createNamedParameter('')),
                    $queryBuilder->expr()->eq('email', $queryBuilder->createNamedParameter(''))
                )
            );

        $rows = $queryBuilder->execute()->fetchAll();

        if (count($rows) === 0) {
            $this->output->writeln('All comment records look valid. Good job!');
            return true;
        }

        $message = count($rows) . ' comments with invalid author or email have been found.';
        $message .= ' Make sure to fix those records!';
        $this->output->writeln($message);

        $commentList = array_map(static fn ($comment) => $comment['uid'], $rows);
        $this->output->writeln('List of comment UIDs: '.implode(', ', $commentList));

        return false;
    }
}
