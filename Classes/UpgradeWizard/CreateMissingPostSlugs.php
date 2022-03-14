<?php

namespace FelixNagel\T3extblog\UpgradeWizard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class CreateMissingPostSlugs extends AbstractSlugUpgradeWizard
{
    public function getTitle(): string
    {
        return 'T3extblog: Create missing post URL slugs';
    }

    public function getDescription(): string
    {
        return 'Create '.$this->countMissingSlugs().' missing post URL slugs.';
    }

    public function executeUpdate(): bool
    {
        $count = $this->createMissingSlugs('tx_t3blog_post', 'url_segment', 100);
        $this->output->writeln($count.' missing post record slugs have been updated.');

        return true;
    }

    public function updateNecessary(): bool
    {
        return $this->isFieldAvailable('tx_t3blog_post', 'url_segment')
            && $this->countMissingSlugs() > 0;
    }
}
