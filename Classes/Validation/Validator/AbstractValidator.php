<?php

namespace FelixNagel\T3extblog\Validation\Validator;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Service\SettingsService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator as CoreAbstractValidator;

/**
 * AbstractValidator
 */
abstract class AbstractValidator extends CoreAbstractValidator
{
    protected function getConfiguration(string $key = null): array
    {
        $settingsService = GeneralUtility::makeInstance(SettingsService::class);

        if ($key === null) {
            return $settingsService->getTypoScriptSettings();
        }

        return $settingsService->getTypoScriptByPath($key);
    }
}
