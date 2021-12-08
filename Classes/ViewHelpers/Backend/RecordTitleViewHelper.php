<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Get record title view helper.
 */
class RecordTitleViewHelper extends AbstractBackendViewHelper
{
    /**
     * Arguments initialization.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('table', 'string', 'Table name', true);
        $this->registerArgument('uid', 'int', 'Record UID', true);
    }

    
    public function render(): string
    {
        $table = $this->arguments['table'];
        $uid = $this->arguments['uid'];

        $row = BackendUtility::getRecord($table, (int) $uid);

        return BackendUtility::getRecordTitle($table, $row);
    }
}
