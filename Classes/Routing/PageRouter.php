<?php
namespace FelixNagel\T3extblog\Routing;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Core\Routing\Aspect\StaticMappableAspectInterface;
use TYPO3\CMS\Core\Routing\PageRouter as CorePageRouter;
use TYPO3\CMS\Core\Routing\Route;

/**
 * PageRouter
 */
class PageRouter extends CorePageRouter
{
    /**
     * Extend max number of mappers as we have a large amount due to year / month / day / post URI concept
     *
     * @todo Try to solve this more elegant
     *
     * @inheritdoc
     */
    protected function assertMaximumStaticMappableAmount(Route $route, array $variableNames = [])
    {
        $mappers = $route->filterAspects(
            [StaticMappableAspectInterface::class, \Countable::class],
            $variableNames
        );

        if (empty($mappers)) {
            return;
        }

        $multipliers = array_map('count', $mappers);
        $product = array_product($multipliers);
        if ($product > 100000) {
            throw new \OverflowException(
                'Possible range of all mappers is larger than 100000 items',
                1546882078
            );
        }
    }
}
