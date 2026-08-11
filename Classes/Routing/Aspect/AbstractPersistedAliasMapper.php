<?php
declare(strict_types = 1);

namespace FelixNagel\T3extblog\Routing\Aspect;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Traits\LoggingTrait;
use FelixNagel\T3extblog\Utility\FrontendUtility;
use TYPO3\CMS\Core\Routing\Aspect\PersistedAliasMapper;

/**
 * PostMapper
 */
abstract class AbstractPersistedAliasMapper extends PersistedAliasMapper
{
    use LoggingTrait;

    protected function logNotFound($message)
    {
        // @extensionScannerIgnoreLine
        $this->getLog()->error($message, array_merge(
            [
                'url' => FrontendUtility::getNormalizedParams()->getRequestUrl(),
            ],
            $this->settings
        ));
    }
}
