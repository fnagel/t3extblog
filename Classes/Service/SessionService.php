<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Traits\LoggingTrait;
use FelixNagel\T3extblog\Utility\FrontendUtility;
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
     */
    protected FrontendUserAuthentication $frontendUser;

    /**
     * SessionService constructor.
     */
    public function __construct()
    {
        $this->frontendUser = FrontendUtility::getTsFe()->fe_user;
    }

    public function setData(array $data): void
    {
        $restoredData = $this->restoreFromSession(self::SESSION_DATA_KEY);

        if ($restoredData) {
            $data = array_merge($restoredData, $data);
        }

        $this->writeToSession(self::SESSION_DATA_KEY, $data);
    }

    public function getData(): ?array
    {
        return $this->restoreFromSession(self::SESSION_DATA_KEY);
    }

    public function removeData(): void
    {
        $this->writeToSession(self::SESSION_DATA_KEY, '');
    }

    public function getDataByKey($key): ?array
    {
        $data = $this->restoreFromSession(self::SESSION_DATA_KEY);

        if (is_array($data) && array_key_exists($key, $data)) {
            return $data[$key];
        }

        return null;
    }

    /**
     * Return stored session data.
     */
    private function restoreFromSession(string $key): ?array
    {
        $data = $this->frontendUser->getKey('ses', 'tx_t3extblog_'.$key);

        $this->getLog()->dev('Get from FE session', $data ?: []);

        return $data ? (array) $data: null;
    }

    /**
     * Write session data.
     */
    private function writeToSession(string $key, array|string $data)
    {
        $this->getLog()->dev('Write to FE session', $data ?: []);

        $this->frontendUser->setKey('ses', 'tx_t3extblog_'.$key, $data);
        // @extensionScannerIgnoreLine
        $this->frontendUser->storeSessionData();
    }
}
