<?php

namespace FelixNagel\T3extblog\Utility;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\MathUtility;
use FelixNagel\T3extblog\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * Checks TypoScript settings.
 */
class TypoScriptValidator
{
    /**
     * Check needed plugin TS settings.
     *
     * Please note: When in BE context this checks for 'module.*' settings!
     *
     * We do not check mailFrom email here as there is a TYPO3 CMS default fallback.
     *
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
     */
    public static function validateSettings(array $settings = null): void
    {
        $key = 'plugin';

        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            $key = 'module';
        }

        $key .= '.tx_t3extblog.settings';

        if (empty($settings) || !is_array($settings)) {
            throw new InvalidConfigurationException(
                'No valid TypoScript settings detected. Make sure '.$key.' is set.',
                1344375015
            );
        }

        if (empty($settings['blogName'])) {
            throw new InvalidConfigurationException(
                'No blog name detected. Make sure '.$key.'.blogName is set.',
                1344375016
            );
        }

        if (empty($settings['blogsystem']['pid']) || !MathUtility::canBeInterpretedAsInteger($settings['blogsystem']['pid'])) {
            throw new InvalidConfigurationException(
                'No blogsystem pid detected. Make sure '.$key.'.blogsystem.pid is set.',
                1344375017
            );
        }

        if (
            empty($settings['subscriptionManager']['pid']) ||
            !MathUtility::canBeInterpretedAsInteger($settings['subscriptionManager']['pid'])
        ) {
            throw new InvalidConfigurationException(
                'No subscription manager pid detected. Make sure '.$key.'.subscriptionManager.pid is set.',
                1344375018
            );
        }

        if (empty($settings['subscriptionManager']['comment']['admin']['mailTo']['email'])) {
            throw new InvalidConfigurationException(
                'No subscription manager admin email receiver address detected.
				Make sure '.$key.'.subscriptionManager.comment.admin.mailTo.email is set.',
                1344375019
            );
        }
    }

    /**
     * Check needed framework configuration.
     */
    public static function validateFrameworkConfiguration(array $settings): void
    {
        if (
            empty($settings['persistence']['storagePid']) ||
            !MathUtility::canBeInterpretedAsInteger($settings['persistence']['storagePid'])
        ) {
            throw new InvalidConfigurationException(
                'No valid persistence storage pid setting detected.
				Make sure plugin.tx_t3extblog.persistence.storagePid is a valid page uid.',
                1344375015
            );
        }
    }
}
