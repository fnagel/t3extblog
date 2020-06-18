<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * LoggingServiceInterface.
 */
interface LoggingServiceInterface
{
    /**
     * Error logging.
     *
     * @param string $msg  Message
     * @param array  $data Data
     */
    public function error($msg, array $data = []);

    /**
     * Exception logging.
     *
     * @param \Exception $exception
     * @param array      $data Data
     */
    public function exception(\Exception $exception, array $data = []);

    /**
     * Notice logging.
     *
     * @param string $msg  Message
     * @param array  $data Data
     */
    public function notice($msg, array $data = []);

    /**
     * Development logging.
     *
     * @param string $msg  Message
     * @param array  $data Data
     */
    public function dev($msg, array $data = []);
}
