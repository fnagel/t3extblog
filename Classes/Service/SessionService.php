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
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * SessionService.
 */
class SessionService implements SessionServiceInterface
{
    use LoggingTrait;

    /**
     * @var string
     */
    public const SESSION_DATA_KEY = 'subscription_session';

    /**
     * Frontend user authentication.
     *
     * @var FrontendUserAuthentication
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
     * @inheritDoc
     */
    public function setData(array $data)
    {
        $oldData = $this->restoreFromSession(self::SESSION_DATA_KEY);

        if (is_array($oldData)) {
            $this->writeToSession(self::SESSION_DATA_KEY, array_merge($oldData, $data));
        } else {
            $this->writeToSession(self::SESSION_DATA_KEY, $data);
        }
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->restoreFromSession(self::SESSION_DATA_KEY);
    }

    /**
     * @inheritDoc
     */
    public function removeData()
    {
        $this->writeToSession(self::SESSION_DATA_KEY, '');
    }

    /**
     * @inheritDoc
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
     *
     * @param string $key
     * @return array|string
     */
    private function restoreFromSession($key)
    {
        $data = $this->frontendUser->getKey('ses', 'tx_t3extblog_'.$key);

        $this->getLog()->dev('Get from FE session', $data ?: []);

        return $data;
    }

    /**
     * Write session data.
     *
     * @param string $key
     * @param array|string $data
     */
    private function writeToSession($key, $data)
    {
        $this->getLog()->dev('Write to FE session', $data ?: []);

        $this->frontendUser->setKey('ses', 'tx_t3extblog_'.$key, $data);
        $this->frontendUser->storeSessionData();
    }
}
