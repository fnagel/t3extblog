<?php

namespace TYPO3\T3extblog\Validation\Validator;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2015 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validator for URLs.
 */
class UrlValidator extends AbstractValidator
{
    /**
     * Returns TRUE, if the given property ($propertyValue) is a valid URL / URI.
     *
     * If at least one error occurred, the result is FALSE.
     *
     * @param mixed $value The value that should be validated
     *
     * @return bool TRUE if the value is valid, FALSE if an error occured
     */
    public function isValid($value)
    {
        if (empty($value)) {
            return true;
        }

        if (!in_array(parse_url($value, PHP_URL_SCHEME), array('http', 'https'), true) ||
            GeneralUtility::isValidUrl($value) === false
        ) {
            $this->addError('The given subject was not a valid URL.', 1392679659);

            return false;
        }

        return true;
    }
}
