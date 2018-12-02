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

use FelixNagel\T3extblog\Domain\Model\AbstractEntity;
use FelixNagel\T3extblog\Domain\Model\Comment;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator for the privacy policy checkbox that respects,
 * if the privacy policy checkbox usage is enabled or not.
 */
class PrivacyPolicyValidator extends AbstractValidator
{
    /**
     * @var array
     */
    protected $supportedOptions = [
        'key' => ['blog', 'TypoScript settings key, either "blog" or "comment"', 'string'],
        'property' => [null, 'Property to add the error to', 'string'],
    ];

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
     */
    protected function isValid($value)
    {
        if ($this->options['property'] !== null &&
            ($value instanceof AbstractEntity && !$value->_hasProperty($this->options['property']))
        ) {
            throw new \TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException(
                'Invalid model property!',
                1543775154
            );
        }

        if ($value instanceof Comment) {
            $value = $value->_getProperty($this->options['property']);
        }

        $configuration = $this->getConfiguration($this->options['key']);

        if ($configuration['privacyPolicy']['enabled'] && !$value) {
            $error = new Error('Please accept the privacy policy.', 1526564974);

            if ($this->options['property'] === null) {
                $this->result->addError($error);
            } else {
                $this->result->forProperty($this->options['property'])->addError($error);
            }
        }
    }

    /**
     * @param string $key
     * @return array
     */
    protected function getConfiguration($key)
    {
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            't3extblog'
        );
        $configuration = [
            'blog' => $settings['blogSubscription'],
            'comment' => $settings['blogsystem']['comments'],
        ];

        if (!array_key_exists($key, $configuration)) {
            throw new \TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException(
                'Invalid TypoScript settings key!',
                1543528851
            );
        }

        return $configuration[$key];
    }
}
