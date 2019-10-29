<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Handles spam check.
 */
interface SpamCheckServiceInterface
{
    /**
     * Checks GET / POST parameter for SPAM.
     *
     * @var array
     *
     * @return int SPAM points
     */
    public function process($settings);
}
