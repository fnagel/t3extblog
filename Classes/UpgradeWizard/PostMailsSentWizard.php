<?php

namespace FelixNagel\T3extblog\UpgradeWizard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Install\Attribute\UpgradeWizard;

#[UpgradeWizard(PostMailsSentWizard::class)]
class PostMailsSentWizard extends AbstractMailsSentWizard
{
    public function getTitle(): string
    {
        return 'T3extblog: Set "mails_sent" flag for existing posts';
    }

    public function executeUpdate(): bool
    {
        $count = $this->updateRecordsForMailsSent('tx_t3blog_post');
        $this->output->writeln($count.' posts have been updated.');

        return true;
    }

    public function updateNecessary(): bool
    {
        return $this->isFieldAvailable('tx_t3blog_post', 'mails_sent');
    }
}
