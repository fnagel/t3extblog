<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\GeneralUtility;

/**
 * SessionService.
 */
class SessionService implements SessionServiceInterface
{
    const SESSION_DATA_KEY = 'subscription_session';

    /**
     * Frontend user authentication.
     *
     * @var \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected $frontendUser;

    /**
     * Logging Service.
     *
     * @var LoggingServiceInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $log;

    /**
     * SessionService constructor.
     *
     * @param LoggingServiceInterface $log
     */
    public function __construct(LoggingServiceInterface $log)
    {
        $this->log = $log;
        $this->frontendUser = GeneralUtility::getTsFe()->fe_user;
    }

    /**
     * @param array $data Data array to save
     */
    public function setData($data)
    {
        $oldData = $this->restoreFromSession(self::SESSION_DATA_KEY);

        if (is_array($oldData)) {
            $this->writeToSession(self::SESSION_DATA_KEY, array_merge($oldData, $data));
        } else {
            $this->writeToSession(self::SESSION_DATA_KEY, $data);
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->restoreFromSession(self::SESSION_DATA_KEY);
    }

    /**
     * @return array
     */
    public function removeData()
    {
        $this->writeToSession(self::SESSION_DATA_KEY, '');
    }

    /**
     * @param string $key
     *
     * @return array
     */
    public function getDataByKey($key)
    {
        $data = $this->restoreFromSession(self::SESSION_DATA_KEY);

        if (is_array($data) && $data[$key]) {
            return $data[$key];
        }

        return;
    }

    /**
     * Return stored session data.
     */
    private function restoreFromSession($key)
    {
        return $this->frontendUser->getKey('ses', 'tx_t3extblog_'.$key);
    }

    /**
     * Write session data.
     */
    private function writeToSession($key, $data)
    {
        $this->log->dev('Write so FE session', $data);

        $this->frontendUser->setKey('ses', 'tx_t3extblog_'.$key, $data);
        $this->frontendUser->storeSessionData();
    }
}
