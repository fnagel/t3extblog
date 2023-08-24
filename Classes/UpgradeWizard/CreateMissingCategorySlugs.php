<?php

namespace FelixNagel\T3extblog\UpgradeWizard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class CreateMissingCategorySlugs extends AbstractSlugUpgradeWizard
{
    public function getTitle(): string
    {
        return 'T3extblog: Create missing category URL slugs';
    }

    public function getDescription(): string
    {
        return 'Create '.$this->countMissingSlugs('tx_t3blog_cat').' missing category URL slugs.';
    }

    public function executeUpdate(): bool
    {
        $count = $this->createMissingSlugs('tx_t3blog_cat', 'url_segment', 100);
        $this->output->writeln($count.' missing category record slugs have been updated.');

        return true;
    }

    public function updateNecessary(): bool
    {
        return $this->isFieldAvailable('tx_t3blog_cat', 'url_segment')
            && $this->countMissingSlugs('tx_t3blog_cat') > 0;
    }
}
