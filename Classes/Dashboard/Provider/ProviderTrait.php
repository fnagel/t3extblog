<?php

namespace FelixNagel\T3extblog\Dashboard\Provider;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\BlogPageSearchUtility;
use FelixNagel\T3extblog\Utility\PermissionUtility;

trait ProviderTrait
{
    /**
     * Returns the UIDs of all blog pages the current backend user has access to.
     *
     * @todo Add dashboard specific TSconfig based PID configuration
     * @return int[]
     */
    protected function getBlogPageUids(): array
    {
        return array_map(
            intval(...),
            PermissionUtility::filterAccessiblePages(BlogPageSearchUtility::getBlogPageUids())
        );
    }
}
