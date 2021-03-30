<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Handles spam check.
 */
class SpamCheckService implements SpamCheckServiceInterface
{
    /**
     * @var Dispatcher
     */
    protected $signalSlotDispatcher;

    /**
     * SpamCheckService constructor.
     *
     * @param Dispatcher $signalSlotDispatcher
     */
    public function __construct(Dispatcher $signalSlotDispatcher)
    {
        $this->signalSlotDispatcher = $signalSlotDispatcher;
    }

    /**
     * Checks GET / POST parameter for SPAM.
     *
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
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
            self::class,
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
