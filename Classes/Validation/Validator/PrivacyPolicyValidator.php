<?php

namespace FelixNagel\T3extblog\Validation\Validator;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\AbstractEntity;
use FelixNagel\T3extblog\Domain\Model\Comment;
use TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException;

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
     * @inheritDoc
     */
    protected function isValid($value)
    {
        if ($this->options['property'] !== null &&
            ($value instanceof AbstractEntity && !$value->_hasProperty($this->options['property']))
        ) {
            throw new InvalidValidationOptionsException(
                'Invalid model property!',
                1543775154
            );
        }

        if ($value instanceof Comment) {
            $value = $value->_getProperty($this->options['property']);
        }

        $configuration = $this->getPrivacyPolicyConfiguration($this->options['key']);

        if ($configuration['privacyPolicy']['enabled'] && !$value) {
            $message = 'Please accept the privacy policy.';

            if ($this->options['property'] === null) {
                $this->addError($message, 1526564974);
            } else {
                $this->addErrorForProperty($this->options['property'], $message, 1526564975);
            }
        }
    }

    protected function getPrivacyPolicyConfiguration(string $key): array
    {
        $settings = $this->getConfiguration();
        $configuration = [
            'blog' => $settings['blogSubscription'],
            'comment' => $settings['blogsystem']['comments'],
        ];

        if (!array_key_exists($key, $configuration)) {
            throw new InvalidValidationOptionsException(
                'Invalid privacy policy TypoScript settings key!',
                1543528851
            );
        }

        return $configuration[$key];
    }
}
