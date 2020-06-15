<?php

namespace FelixNagel\T3extblog\Validation\Validator;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Service\SettingsService;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator as CoreAbstractValidator;

/**
 * AbstractValidator
 */
abstract class AbstractValidator extends CoreAbstractValidator
{
    /**
     * The SettingsService
     *
     * @var SettingsService
     */
    protected $settingsService;

    /**
     * Inject the SettingsService
     *
     * @param SettingsService $settingsService
     */
    public function injectSettingsService(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * @param string $key
     * @return array
     */
    protected function getConfiguration($key = null)
    {
        if ($key === null) {
            return $this->settingsService->getTypoScriptSettings();
        } else {
            return $this->settingsService->getTypoScriptByPath($key);
        }
    }
}
