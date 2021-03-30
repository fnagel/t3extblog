<?php

namespace FelixNagel\T3extblog\Validation\Validator;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator for URLs.
 */
class UrlValidator extends AbstractValidator
{
    /**
     * Returns TRUE, if the given value is a valid URL / URI.
     *
     * @param mixed $value The value that should be validated
     *
     * @return bool TRUE if the value is valid, FALSE if an error occurred
     */
    protected function isValid($value)
    {
        if (!in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true) ||
            !GeneralUtility::isValidUrl($value)
        ) {
            $this->addError('The given subject was not a valid URL.', 1392679659);

            return false;
        }

        return true;
    }
}
