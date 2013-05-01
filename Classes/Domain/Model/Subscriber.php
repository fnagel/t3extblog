<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Felix Nagel <info@felixnagel.com>
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
 *
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Domain_Model_Subscriber extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * email
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $email;

	/**
	 * name
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * postUid
	 *
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $postUid;

	/**
	 * lastSent
	 *
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $lastSent;

	/**
	 * code
	 *
	 * @var string
	 * @validate NotEmpty
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
	 * @return void
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the postUid
	 *
	 * @return integer $postUid
	 */
	public function getPostUid() {
		return $this->postUid;
	}

	/**
	 * Sets the postUid
	 *
	 * @param integer $postUid
	 * @return void
	 */
	public function setPostUid($postUid) {
		$this->postUid = $postUid;
	}

	/**
	 * Returns the lastSent
	 *
	 * @return integer $lastSent
	 */
	public function getLastSent() {
		return $this->lastSent;
	}

	/**
	 * Sets the lastSent
	 *
	 * @param integer $lastSent
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
	 * @return void
	 */
	public function setCode($code) {
		$this->code = $code;
	}

}
?>