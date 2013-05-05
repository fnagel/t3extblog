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
class Tx_T3extblog_Service_FrontendUserService {

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->frontendUser = $GLOBALS['TSFE']->fe_user;
	}

	/**
	 *
	 * @return boolean
	 */
	public function hasAuth() {
		 return $this->restoreFromSession("auth");
	}

	/**
	 *
	 */
	public function authValid() {
		 $this->writeToSession("auth", TRUE);
	}

	/**
	 *
	 * @return boolean
	 */
	public function setEmail($email) {
		 $this->writeToSession("email", $email);
	}

	/**
	 *
	 * @return boolean
	 */
	public function getEmail() {
		 return $this->restoreFromSession("email");
	}
	
    /**
     * Return stored session data
     */
    private function restoreFromSession($key) {
        return $this->frontendUser->getKey('ses', 'tx_t3extblog_' . $key);
    }
 
    /**
     * Write session data
     */
    private function writeToSession($key, $data) {
		$this->frontendUser->setKey('ses', 'tx_t3extblog_' . $key, $data);
		$this->frontendUser->storeSessionData();
    }
}
?>