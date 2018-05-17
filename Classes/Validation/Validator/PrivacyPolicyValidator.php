<?php

namespace FelixNagel\T3extblog\Validation\Validator;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 sgalinski Internet Services (https://www.sgalinski.de)
 *
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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator for the privacy policy checkbox that respects, if the privacy policy checkbox usage is enabled or not
 *
 * @package TYPO3\T3extblog\Validation\Validator
 * @author Kevin Ditscheid <kevin.ditscheid@sgalinski.de>
 */
class PrivacyPolicyValidator extends AbstractValidator
{

    /**
     * The ConfigurationManager
     *
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * Inject the ConfigurationManager
     *
     * @param ConfigurationManager $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Check if $value is valid. If it is not valid, needs to add an error
     * to result.
     *
     * @param mixed $value The value to check
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function isValid($value)
    {
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            't3extblog');
        if ($settings['blogSubscription']['privacyPolicy']['enabled'] && !$value) {
            $this->addError('Please accept the privacy policy', 1526564974);
        }
    }
}
