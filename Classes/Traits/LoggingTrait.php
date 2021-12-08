<?php

namespace FelixNagel\T3extblog\Traits;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Service\LoggingServiceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * LoggingTrait
 */
trait LoggingTrait
{
    /**
     * Logging Service.
     */
    private ?LoggingServiceInterface $log = null;

    /**
     * @param LoggingServiceInterface $log
     */
    public function injectLog(LoggingServiceInterface $log)
    {
        $this->log = $log;
    }

    /**
     * @return LoggingServiceInterface
     */
    protected function getLog()
    {
        if ($this->log === null) {
            $this->log = GeneralUtility::makeInstance(LoggingServiceInterface::class);
        }

        return $this->log;
    }
}
