<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handles logging
 * Configured by TYPO3 core log level.
 */
class LoggingService implements LoggingServiceInterface, SingletonInterface
{
    /**
     * The extension key.
     *
     * @var bool
     */
    protected $extKey = 't3extblog';

    /**
     * @var bool
     */
    protected $logInDevlog;

    /**
     * @var bool
     */
    protected $renderInFe;

    /**
     * @var \FelixNagel\T3extblog\Service\SettingsService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $settingsService;

    /**
     * @var array
     */
    protected $settings;

    /**
     * LoggingService constructor.
     *
     * @param SettingsService $settingsService
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Init object.
     */
    public function initializeObject()
    {
        $this->settings = $this->settingsService->getTypoScriptSettings();

        $this->logInDevlog = $this->settings['debug']['logInDevlog'];
        $this->renderInFe = $this->settings['debug']['renderInFe'];
    }

    /**
     * Error logging.
     *
     * @param string $msg  Message
     * @param array  $data Data
     */
    public function error($msg, $data = [])
    {
        $this->writeToSysLog($msg, 3);

        if ($this->renderInFe) {
            $this->outputDebug($msg, 3, $data);
        }

        if ($this->logInDevlog) {
            $this->writeToDevLog($msg, 3, $data);
        }
    }

    /**
     * Notice logging.
     *
     * @param string $msg  Message
     * @param array  $data Data
     */
    public function notice($msg, $data = [])
    {
        $this->writeToSysLog($msg, 1);

        if ($this->renderInFe) {
            $this->outputDebug($msg, 1, $data);
        }

        if ($this->logInDevlog) {
            $this->writeToDevLog($msg, 1, $data);
        }
    }

    /**
     * Development logging.
     *
     * @param string $msg  Message
     * @param array  $data Data
     */
    public function dev($msg, $data = [])
    {
        if ($this->renderInFe) {
            $this->outputDebug($msg, 1, $data);
        }

        if ($this->logInDevlog) {
            $this->writeToDevLog($msg, 1, $data);
        }
    }

    /**
     * Writes message to the FE.
     *
     * @param string $msg      Message (in English).
     * @param int    $severity Severity: 0 is info, 1 is notice, 2 is warning, 3 is error, 4 is fatal error
     * @param array  $data     Data
     */
    protected function outputDebug($msg, $severity = 0, $data = [])
    {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($data, '['.$severity.'] '.$msg);
    }

    /**
     * Logs message to the system log.
     *
     * @param string $msg      Message (in English).
     * @param int    $severity Severity: 0 is info, 1 is notice, 2 is warning, 3 is error, 4 is fatal error
     */
    protected function writeToSysLog($msg, $severity = 0)
    {
        GeneralUtility::sysLog($msg, $this->extKey, $severity);
    }

    /**
     * Logs message to the development log.
     *
     * @param string $msg      Message (in english).
     * @param int    $severity Severity: 0 is info, 1 is notice, 2 is warning, 3 is fatal error, -1 is "OK" message
     * @param mixed  $dataVar  Additional data you want to pass to the logger.
     */
    protected function writeToDevLog($msg, $severity = 0, $dataVar = false)
    {
        GeneralUtility::devLog($msg, $this->extKey, $severity, $dataVar);
    }
}
