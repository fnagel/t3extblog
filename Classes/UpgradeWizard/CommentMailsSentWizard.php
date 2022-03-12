<?php

namespace FelixNagel\T3extblog\UpgradeWizard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class CommentMailsSentWizard extends AbstractMailsSentWizard
{
    public function getTitle(): string {
        return 'T3extblog: Set "mails_sent" flag for existing comments';
    }

    public function executeUpdate(): bool {
        $count = $this->updateRecordsForMailsSent('tx_t3blog_com');
        $this->output->writeln($count.' comments have been updated.');

        return true;
    }

    public function updateNecessary(): bool {
        return $this->isFieldAvailable('tx_t3blog_com', 'mails_sent');
    }
}
