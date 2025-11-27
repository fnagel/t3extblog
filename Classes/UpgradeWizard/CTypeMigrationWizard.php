<?php

namespace FelixNagel\T3extblog\UpgradeWizard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

#[UpgradeWizard(CTypeMigrationWizard::class)]
class CTypeMigrationWizard extends AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            't3extblog_blogsystem' => 't3extblog_blogsystem',
            't3extblog_subscriptionmanager' => 't3extblog_subscriptionmanager',
            't3extblog_blogsubscription' => 't3extblog_blogsubscription',
            't3extblog_archive' => 't3extblog_archive',
            't3extblog_rss' => 't3extblog_rss',
            't3extblog_categories' => 't3extblog_categories',
            't3extblog_latestcomments' => 't3extblog_latestcomments',
            't3extblog_latestposts' => 't3extblog_latestposts',
        ];
    }

    public function getTitle(): string
    {
        return 'Migrate "t3extblog" plugins to content elements.';
    }

    public function getDescription(): string
    {
        return 'The "t3extblog" plugin is now registered as content element.'.
            ' Update migrates existing records and backend user permissions.';
    }
}
