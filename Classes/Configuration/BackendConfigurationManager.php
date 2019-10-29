<?php

namespace FelixNagel\T3extblog\Configuration;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * and 9.x when editing a record using the context menu (t3js-clickmenutrigger CSS
     * class). Example: right click on a record in the BE module, click edit, change
     * visibility which should trigger emails but does not.
     *
     * Using a issueCommand VH link works as expected. Not needed for TYPO3 7.x.
     *
     * @todo Rework this: ugly hack but not sure how to solve this in a clean way
     *
     * {@inheritdoc}
     */
    protected function getCurrentPageIdFromGetPostData()
    {
        $id = parent::getCurrentPageIdFromGetPostData();

        if (empty($id)) {
            $id = (int) GeneralUtility::_GP('popViewId');
        }

        return $id;
    }
}
