<?php

namespace FelixNagel\T3extblog\Dashboard\Provider;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

/**
 * AbstractListDataProvider
 *
 * @todo Add limit as we need to pass an array and our pagination will not limit the query any more
 */
abstract class AbstractListDataProvider extends AbstractDataProvider implements ListDataProviderInterface
{

}
