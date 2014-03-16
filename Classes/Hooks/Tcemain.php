<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2014 Felix Nagel <info@felixnagel.com>
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
 * Class being included by TCEmain using a hook
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Hooks_Tcemain {

	// fields to check for changes
	var $watchedFields = array(
		'hidden',
		'approved',
		'spam'
	);

	/**
	 * notificationService
	 *
	 * @var Tx_T3extblog_Service_NotificationService
	 */
	protected $notificationService = NULL;

	/**
	 * Hook: processDatamap_afterDatabaseOperations
	 *
	 * Note: When using the hook after INSERT operations, you will only get the temporary NEW... id passed to your hook as $id,
	 *         but you can easily translate it to the real uid of the inserted record using the $this->substNEWwithIDs array.
	 *
	 * @param    string $status : (reference) Status of the current operation, 'new' or 'update'
	 * @param    string $table : (refrence) The table currently processing data for
	 * @param    string $id : (reference) The record uid currently processing data for, [integer] or [string] (like 'NEW...')
	 * @param    array  $fieldArray : (reference) The field array of a record
	 *
	 * @return    void
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, &$pObj) {
		if ($table == 'tx_t3blog_com') {

			if ($status == 'update') {
				// get history Record
				$hr = $pObj->historyRecords[$table . ':' . $id]['newRecord'];

				if (is_array($hr)) {
					$changedfields = array_keys($hr);

					if (is_array($changedfields) === TRUE) {
						$updatefields = array_intersect($changedfields, $this->watchedFields);

						if (is_array($updatefields) === TRUE && count($updatefields) > 0) {
							$this->getNotificationService()->processCommentStatusChanged($id);
						}
					}
				}
			}

			if ($status == 'new') {
				$this->getNotificationService()->processCommentAdded($id, false);
			}
		}
	}

	/**
	 * Get notification service
	 *
	 * @return Tx_T3extblog_Service_NotificationService
	 */
	protected function getNotificationService() {
		if ($this->notificationService == NULL) {
			t3lib_div::requireOnce(t3lib_extMgm::extPath('t3extblog', 'Classes/Service/NotificationService.php'));
			$this->notificationService = t3lib_div::makeInstance("Tx_T3extblog_Service_NotificationService");
		}

		return $this->notificationService;
	}

}

?>