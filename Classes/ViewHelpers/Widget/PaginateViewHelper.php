<?php

namespace FelixNagel\T3extblog\ViewHelpers\Widget;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016-2018 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper;

/**
 * PaginateViewHelper.
 */
class PaginateViewHelper extends AbstractWidgetViewHelper
{
    /**
     * @var \FelixNagel\T3extblog\ViewHelpers\Widget\Controller\PaginateController
     */
    protected $controller;

    /**
     * @param \FelixNagel\T3extblog\ViewHelpers\Widget\Controller\PaginateController $controller
     */
    public function injectPaginateController(\FelixNagel\T3extblog\ViewHelpers\Widget\Controller\PaginateController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param mixed  $objects
     * @param string $as
     * @param array  $configuration
     *
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    public function render($objects, $as, array $configuration = ['itemsPerPage' => 10, 'insertAbove' => false, 'insertBelow' => true, 'maximumNumberOfLinks' => 99])
    {
        if (!($objects instanceof QueryResultInterface || $objects instanceof ObjectStorage || is_array($objects))) {
            throw new \UnexpectedValueException('Supplied file object type '.get_class($objects).' must be QueryResultInterface or ObjectStorage or be an array.', 1454510731);
        }

        return $this->initiateSubRequest();
    }
}
