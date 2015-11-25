<?php

namespace TYPO3\T3extblog\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Felix Nagel <info@felixnagel.com>
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

/**
 * AbstractSubscriber
 */
abstract class AbstractSubscriber extends AbstractEntity {

	/**
	 * @var boolean
	 */
	protected $hidden = TRUE;

	/**
	 * @var boolean
	 */
	protected $deleted;

	/**
	 * email
	 *
	 * @var string
	 * @validate NotEmpty
	 * @validate EmailAddress
	 */
	protected $email;

	/**
	 * lastSent
	 *
	 * @var \DateTime
	 */
	protected $lastSent = NULL;

	/**
	 * code
	 *
	 * @var string
	 */
	protected $code;

	/**
	 * Returns the email
	 *
	 * @return string $email
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Sets the email
	 *
	 * @param string $email
	 *
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Returns the lastSent
	 *
	 * @return \DateTime $lastSent
	 */
	public function getLastSent() {
		return $this->lastSent;
	}

	/**
	 * Sets the lastSent
	 *
	 * @param \DateTime $lastSent
	 *
	 * @return void
	 */
	public function setLastSent($lastSent) {
		$this->lastSent = $lastSent;
	}

	/**
	 * Returns the code
	 *
	 * @return string $code
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * Sets the code
	 *
	 * @param string $code
	 *
	 * @return void
	 */
	public function setCode($code) {
		$this->code = $code;
	}

	/**
	 * Creates a code
	 *
	 * @return void
	 */
	protected function createCode() {
		$now = new \DateTime();
		$input = $this->email . $now->getTimestamp() . uniqid();

		$this->code = substr(GeneralUtility::hmac($input), 0, 32);
	}

	/**
	 * Update subscriber
	 *
	 * @return void
	 */
	public function updateAuth() {
		$this->setLastSent(new \DateTime());
		$this->createCode();
	}

	/**
	 * Returns prepared mailto array
	 *
	 * @return array
	 */
	public function getMailTo() {
		return array($this->getEmail() => $this->getName());
	}

	/**
	 * Checks if the authCode is still valid
	 *
	 * @param string $expireDate
	 *
	 * @return boolean
	 */
	public function isAuthCodeExpired($expireDate) {
		$now = new \DateTime();
		$expire = clone $this->getLastSent();

		if ($now > $expire->modify($expireDate)) {
			return TRUE;
		}

		return FALSE;
	}
}
