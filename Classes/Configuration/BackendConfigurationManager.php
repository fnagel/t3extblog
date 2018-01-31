<?php

namespace TYPO3\T3extblog\Configuration;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\T3extblog\Service\SettingsService;

/**
 * A general purpose configuration manager used in backend mode.
 */
class BackendConfigurationManager extends \TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager
{
    /**
     * Make sure TS is generated from currently selected page.
     *
     * Needed basically to make extbase work. Without PID is set to 0 (which is root)
     * and persistence, TS generation, etc. will fail. This is the case in TYPO3 8.x
     * when editing a record using the context menu (t3-js-clickmenutrigger CSS class).
     *
     * Using a issueCommand VH link works as expected. Not needed for TYPO3 7.x.
     *
     * @todo Rework this: ugly hack but not sure how to solve this in a clean way
     *
     * {@inheritdoc}
     */
    protected function getCurrentPageIdFromGetPostData()
    {
        return SettingsService::gePageIdFromGetPostData();
    }
}
