<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2018 Felix Nagel <info@felixnagel.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

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

    /**
     * @return string
     */
    public function render()
    {
        $table = $this->arguments['table'];
        $uid = $this->arguments['uid'];

        $row = BackendUtility::getRecord($table, (int) $uid);

        return BackendUtility::getRecordTitle($table, $row);
    }
}
