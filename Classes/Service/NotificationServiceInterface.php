<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
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
 * Interface for Notification Service
 *
 * This class should send emails (admin, comment author opt-in, subscription)
 * when receiving new or changed comments.
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
interface Tx_T3extblog_Service_NotificationServiceInterface {

	/**
	 * Process added comment
	 * Comment is already persisted to DB
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment Comment uid
	 * @param boolean $notifyAdmin
	 *
	 * @return void
	 */
	public function processCommentAdded(Tx_T3extblog_Domain_Model_Comment $comment, $notifyAdmin = true);

	/**
	 * Process changed status of a comment
	 * Comment is already persisted to DB
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment Comment uid
	 *
	 * @return void
	 */
	public function processCommentStatusChanged(Tx_T3extblog_Domain_Model_Comment $comment);

}

?>