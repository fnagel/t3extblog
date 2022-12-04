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
    protected Dispatcher $signalSlotDispatcher;

    /**
     * SpamCheckService constructor.
     *
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
     */
    public function process(array $settings): int
    {
        $arguments = GeneralUtility::_GPmerged('tx_t3extblog');
        $commentArguments = GeneralUtility::_POST('tx_t3extblog_blogsystem')['newComment'];
        $spamPoints = 0;

        if (!$settings['enable']) {
            return $spamPoints;
        }

        if ($settings['honeypot'] && !$this->checkHoneyPotFields($arguments)) {
            $spamPoints += (int) $settings['honeypot'];
        }

        if ($settings['isHumanCheckbox'] && (!isset($arguments['human']) && empty($arguments['human']))) {
            $spamPoints += (int) $settings['isHumanCheckbox'];
        }

        if ($settings['cookie'] && !$_COOKIE['fe_typo_user']) {
            $spamPoints += (int) $settings['cookie'];
        }

        if ($settings['userAgent'] && GeneralUtility::getIndpEnv('HTTP_USER_AGENT') === '') {
            $spamPoints += (int) $settings['userAgent'];
        }

        if ($settings['link'] && $this->checkTextForLinks($commentArguments['text'])) {
            $spamPoints += (int) $settings['link'];
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
     */
    protected function checkHoneyPotFields(array $arguments): bool
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

    protected function checkTextForLinks(string $string): bool
    {
        return preg_match('/(.*)(:)(\/\/)?(.*)(\..*)?/', $string) > 0;
    }
}
