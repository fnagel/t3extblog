<?php

namespace FelixNagel\T3extblog\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use FelixNagel\T3extblog\Utility\GeneralUtility;

/**
 * SessionService.
 */
class SessionService implements SessionServiceInterface
{
    const SESSION_DATA_KEY = 'subscription_session';

    /**
     * Logging Service.
     *
     * @var \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected $frontendUser;

    /**
     * Logging Service.
     *
     * @var \FelixNagel\T3extblog\Service\LoggingServiceInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $log;

    /**
     * __construct.
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
