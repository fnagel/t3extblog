<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Handles logging
 */
class LoggingService implements LoggingServiceInterface, SingletonInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var bool
     */
    protected $renderInFe = false;

    /**
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * @var array
     */
    protected $settings = [];

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
        $this->renderInFe = $this->settingsService->getTypoScriptByPath('debug.renderInFe');
    }

    /**
     * @inheritDoc
     */
    public function error($msg, array $data = [])
    {
        $this->logger->critical($msg, $data);
        $this->outputDebug($msg, 'error', $data);
    }

    /**
     * @inheritDoc
     */
    public function exception(\Exception $exception, array $data = [])
    {
        $this->logger->alert($exception->getMessage(), array_merge(
            [
                'code' => $exception->getCode(),
            ],
            $data
        ));
    }

    /**
     * @inheritDoc
     */
    public function notice($msg, array $data = [])
    {
        $this->logger->notice($msg, $data);
        $this->outputDebug($msg, 'notice', $data);
    }

    /**
     * @inheritDoc
     */
    public function dev($msg, array $data = [])
    {
        $this->logger->debug($msg, $data);
        $this->outputDebug($msg, 'debug', $data);
    }

    /**
     * Writes message to the FE.
     *
     * @param string $msg      Message (in English)
     * @param string $severity Severity
     * @param array  $data     Data
     */
    protected function outputDebug($msg, $severity = 'debug', $data = [])
    {
        if ($this->renderInFe) {
            DebuggerUtility::var_dump($data, '['.$severity.'] '.$msg);
        }
    }
}
