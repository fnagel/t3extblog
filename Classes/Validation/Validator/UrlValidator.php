<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Felix Nagel <info@felixnagel.com>
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

/**
 * Validator for URLs
 *
 * @package t3extblog
 */
class Tx_T3extblog_Validation_Validator_UrlValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {

	/**
	 * Returns TRUE, if the given property ($propertyValue) is a valid URL / URI.
	 *
	 * If at least one error occurred, the result is FALSE.
	 *
	 * @param mixed $value The value that should be validated
	 *
	 * @return boolean TRUE if the value is valid, FALSE if an error occured
	 */
	public function isValid($value) {
		if (empty($value)) {
			return TRUE;
		}

		if (t3lib_div::isValidUrl($value) === FALSE) {
			$this->addError('The given subject was not a valid URL.', 1392679659);

			return FALSE;
		}

		return TRUE;
	}
}

?>