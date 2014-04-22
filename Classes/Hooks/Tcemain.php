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

	/**
	 * Fields to check for changes
	 *
	 * @var array
	 */
	var $watchedFields = array(
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
	 * objectContainer
	 *
	 * @var Tx_Extbase_Object_Container_Container
	 */
	protected $objectContainer = NULL;

	/**
	 * Processes item deletion
	 *
	 * @param string $command
	 * @param string $table
	 * @param mixed $id
	 *
	 * @return void
	 */
	public function processCmdmap_postProcess($command, $table, $id) {
		if ($command == 'delete') {
			if ($table == 'tx_t3blog_post') {
				$this->deletePostRelations(intval($id));
			}
		}
	}

	/**
	 * Hook: processDatamap_afterDatabaseOperations
	 *
	 * Note: When using the hook after INSERT operations, you will only get the temporary NEW... id passed to your hook as $id,
	 *         but you can easily translate it to the real uid of the inserted record using the $this->substNEWwithIDs array.
	 *
	 * @param    string $status : (reference) Status of the current operation, 'new' or 'update'
	 * @param    string $table : (refrence) The table currently processing data for
	 * @param    string $id : (reference) The record uid currently processing data for, [integer] or [string] (like 'NEW...')
	 * @param    array $fields : (reference) The field array of a record     *
	 * @param           $tceMain
	 *
	 * @internal param \t3lib_TCEmain $tce
	 *
	 * @return    void
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, $fields, $tceMain) {
		$pid = $tceMain->checkValue_currentRecord['pid'];
		if (!is_numeric($id)) {
			$id = $tceMain->substNEWwithIDs[$id];
		}

		if ($table == 'tx_t3blog_post') {
			if (isset($GLOBALS['_POST']['_savedokview_x'])) {
				$this->processPreview($id);
			}
		}

		if ($table == 'tx_t3blog_com') {
			if ($status == 'update') {
				if ($this->isUpdateNeeded($table, $id, $tceMain, $this->watchedFields)) {
					$this->processChangedComment($id, $pid);
				}
			}

			if ($status == 'new') {
				$this->processNewComment($id, $fields['pid']);
			}
		}
	}

	/**
	 * Deletes all data associated with the post when post is deleted
	 *
	 * @param int $id
	 *
	 * @return void
	 */
	protected function deletePostRelations($id) {
		$command = array(
			'tx_t3blog_com' => $this->getDeleteArrayForTable($id, 'tx_t3blog_com', 'fk_post'),
			'tx_t3blog_com_nl' => $this->getDeleteArrayForTable($id, 'tx_t3blog_com_nl', 'post_uid'),
			'tx_t3blog_trackback' => $this->getDeleteArrayForTable($id, 'tx_t3blog_trackback', 'postid'),
			'tt_content' => $this->getDeleteArrayForTable($id, 'tt_content', 'irre_parentid', ' AND irre_parenttable=\'tx_t3blog_post\'')
		);

		/* @var $tce t3lib_TCEmain */
		$tceMain = $this->getObjectContainer()->getInstance('t3lib_TCEmain');

		$tceMain->start(array(), $command);
		$tceMain->process_cmdmap();
	}

	/**
	 * @param integer $postId
	 * @param string $tableName
	 * @param string $fieldName
	 * @param string $extraWhere
	 *
	 * @return array
	 */
	protected function getDeleteArrayForTable($postId, $tableName, $fieldName, $extraWhere = '') {
		$command = array();

		$where = $fieldName . '=' . $postId . t3lib_BEfunc::deleteClause($tableName) . $extraWhere;

		$data = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('uid', $tableName, $where);
		foreach ($data as $record) {
			$command[$record['uid']]['delete'] = 1;
		}

		return $command;
	}

	/**
	 * @param integer $id
	 */
	protected function processPreview($id) {
		$pagesTsConfig = t3lib_BEfunc::getPagesTSconfig($GLOBALS['_POST']['popViewId']);

		if ($pagesTsConfig['tx_t3extblog.']['singlePid']) {
			$record = t3lib_BEfunc::getRecord('tx_t3blog_post', $id);

			$parameters = array(
//				'tx_t3extblog_blogsystem[controller]' => 'Post',
				'tx_t3extblog_blogsystem[action]' => 'preview',
				'tx_t3extblog_blogsystem[previewPost]' => $record['uid'],
//				'no_cache' => 1,
			);
			if ($record['sys_language_uid'] > 0) {
				if ($record['l10n_parent'] > 0) {
					$parameters['tx_t3extblog_blogsystem[previewPost]'] = $record['l10n_parent'];
				}
				$parameters['L'] = $record['sys_language_uid'];
			}

			$GLOBALS['_POST']['popViewId_addParams'] = t3lib_div::implodeArrayForUrl('', $parameters, '', FALSE, TRUE);
			$GLOBALS['_POST']['popViewId'] = $pagesTsConfig['tx_t3extblog.']['singlePid'];
		}
	}


	/**
	 * @param integer $id
	 * @param integer $pid
	 *
	 * @internal param int $fields
	 */
	protected function processNewComment($id, $pid) {
		// extbase fix
		$this->getObjectContainer()
			->getInstance("Tx_T3extblog_Service_SettingsService")
			->setPageUid($pid);

		$this->getNotificationService()->processCommentAdded($this->getComment($id), FALSE);
	}

	/**
	 * @param integer $id
	 * @param integer $pid
	 *
	 * @internal param int $fields
	 */
	protected function processChangedComment($id, $pid) {
		// extbase fix
		$this->getObjectContainer()
			->getInstance("Tx_T3extblog_Service_SettingsService")
			->setPageUid($pid);

		$this->getNotificationService()->processCommentStatusChanged($this->getComment($id));
	}

	/**
	 * Get comment
	 *
	 * @param integer $uid Page uid
	 *
	 * @return Tx_T3extblog_Domain_Model_Comment
	 */
	protected function getComment($uid) {
		$commentRepository = $this->getObjectContainer()->getInstance("Tx_T3extblog_Domain_Repository_CommentRepository");
		$comment = $commentRepository->findByUid($uid);

		return $comment;
	}

	/**
	 * Get object container
	 *
	 * @return Tx_Extbase_Object_Container_Container
	 */
	protected function getObjectContainer() {
		if ($this->objectContainer == NULL) {
			$this->objectContainer = t3lib_div::makeInstance("Tx_Extbase_Object_Container_Container");
		}

		return $this->objectContainer;
	}

	/**
	 * Get notification service
	 *
	 * @return Tx_T3extblog_Service_NotificationService
	 */
	protected function getNotificationService() {
		if ($this->notificationService == NULL) {
			$this->notificationService = $this->getObjectContainer()->getInstance("Tx_T3extblog_Service_NotificationService");
		}

		return $this->notificationService;
	}

	/**
	 * @param string $table
	 * @param integer $id
	 * @param t3lib_TCEmain $tceMain
	 * @param array $watchedFields
	 *
	 * @return bool
	 */
	protected function isUpdateNeeded($table, $id, $tceMain, $watchedFields) {
		// get history Record
		$history = $tceMain->historyRecords[$table . ':' . $id]['newRecord'];

		if (is_array($history) === TRUE) {
			$changedfields = array_keys($history);

			if (is_array($changedfields) === TRUE) {
				$updatefields = array_intersect($changedfields, $watchedFields);

				if (is_array($updatefields) === TRUE && count($updatefields) > 0) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}

}

?>