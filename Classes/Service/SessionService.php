<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Traits\LoggingTrait;
use FelixNagel\T3extblog\Utility\GeneralUtility;

/**
 * SessionService.
 */
class SessionService implements SessionServiceInterface
{
    use LoggingTrait;

    const SESSION_DATA_KEY = 'subscription_session';

    /**
     * Frontend user authentication.
     *
     * @var \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected $frontendUser;

    /**
     * SessionService constructor.
     */
    public function __construct()
    {
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
     * @return void
     */
    public function removeData()
    {
        $this->writeToSession(self::SESSION_DATA_KEY, '');
    }

    /**
     * @param string $key
     *
     * @return array|null
     */
    public function getDataByKey($key)
    {
        $data = $this->restoreFromSession(self::SESSION_DATA_KEY);

        if (is_array($data) && $data[$key]) {
            return $data[$key];
        }

        return null;
    }

    /**
     * Return stored session data.
     */
    private function restoreFromSession($key)
    {
        $data = $this->frontendUser->getKey('ses', 'tx_t3extblog_'.$key);

        $this->getLog()->dev('Get from FE session', $data);

        return $data;
    }

    /**
     * Write session data.
     */
    private function writeToSession($key, $data)
    {
        $this->getLog()->dev('Write to FE session', $data);

        $this->frontendUser->setKey('ses', 'tx_t3extblog_'.$key, $data);
        $this->frontendUser->storeSessionData();
    }
}
