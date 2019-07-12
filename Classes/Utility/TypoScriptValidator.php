<?php

namespace FelixNagel\T3extblog\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2018 Felix Nagel <info@felixnagel.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use TYPO3\CMS\Core\Utility\MathUtility;
use FelixNagel\T3extblog\Exception\InvalidConfigurationException;

/**
 * TypoScript Settings Validator class.
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
     * @param array $settings
     *
     * @throws InvalidConfigurationException
     */
    public static function validateSettings(array $settings = null)
    {
        $key = 'plugin';
        if (TYPO3_MODE === 'BE') {
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
     *
     * @param array $settings
     *
     * @throws InvalidConfigurationException
     */
    public static function validateFrameworkConfiguration(array $settings)
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
