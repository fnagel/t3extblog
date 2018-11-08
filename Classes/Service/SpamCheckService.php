<?php

namespace FelixNagel\T3extblog\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2018 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handles spam check.
 */
class SpamCheckService implements SpamCheckServiceInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $signalSlotDispatcher;

    /**
     * Checks GET / POST parameter for SPAM.
     *
     * @var array
     *
     * @return int
     */
    public function process($settings)
    {
        $arguments = GeneralUtility::_GPmerged('tx_t3extblog');
        $spamPoints = 0;

        if (!$settings['enable']) {
            return $spamPoints;
        }

        if ($settings['honeypot']) {
            if (!$this->checkHoneyPotFields($arguments)) {
                $spamPoints += intval($settings['honeypot']);
            }
        }

        if ($settings['isHumanCheckbox']) {
            if (!isset($arguments['human']) && empty($arguments['human'])) {
                $spamPoints += intval($settings['isHumanCheckbox']);
            }
        }

        if ($settings['cookie']) {
            if (!$_COOKIE['fe_typo_user']) {
                $spamPoints += intval($settings['cookie']);
            }
        }

        if ($settings['userAgent']) {
            if (GeneralUtility::getIndpEnv('HTTP_USER_AGENT') == '') {
                $spamPoints += intval($settings['userAgent']);
            }
        }

        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            'spamCheck',
            [$settings, $arguments, &$spamPoints, $this]
        );

        return $spamPoints;
    }

    /**
     * Checks honeypot fields.
     *
     * @param array $arguments
     *
     * @return bool
     */
    protected function checkHoneyPotFields($arguments)
    {
        if (!isset($arguments['author']) || strlen($arguments['author']) > 0) {
            return false;
        }

        if (!isset($arguments['link']) || strlen($arguments['link']) > 0) {
            return false;
        }

        if (!isset($arguments['text']) || strlen($arguments['text']) > 0) {
            return false;
        }

        if (!isset($arguments['timestamp']) || $arguments['timestamp'] !== '1368283172') {
            return false;
        }

        return true;
    }
}
