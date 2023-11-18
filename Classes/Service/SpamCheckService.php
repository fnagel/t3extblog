<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use FelixNagel\T3extblog\Event;

/**
 * Handles spam check.
 */
class SpamCheckService implements SpamCheckServiceInterface
{
    public function __construct(protected readonly EventDispatcherInterface $eventDispatcher)
    {
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

        if ($settings['cookie'] && !isset($_COOKIE['fe_typo_user'])) {
            $spamPoints += (int) $settings['cookie'];
        }

        if ($settings['userAgent'] && GeneralUtility::getIndpEnv('HTTP_USER_AGENT') === '') {
            $spamPoints += (int) $settings['userAgent'];
        }

        if (isset($settings['link']) && $settings['link'] &&
            ($comment = GeneralUtility::_POST('tx_t3extblog_blogsystem')) &&
            isset($comment['newComment']['text']) && $this->checkTextForLinks($comment['newComment']['text'])
        ) {
            $spamPoints += (int) $settings['link'];
        }

        /** @var Event\SpamCheckEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new Event\SpamCheckEvent($settings, $arguments, $spamPoints)
        );

        return $event->getSpamPoints();
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
