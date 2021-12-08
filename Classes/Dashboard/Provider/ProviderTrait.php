<?php

namespace FelixNagel\T3extblog\Dashboard\Provider;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\BlogPageSearchUtility;

trait ProviderTrait
{
    /**
     * @todo Add dashboard specific TSconfig based PID configuration
     *
     */
    protected function getBlogPageUids(): array
    {
        return BlogPageSearchUtility::getBlogPageUids();
    }
}
