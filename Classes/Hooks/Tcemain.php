<?php

namespace TYPO3\T3extblog\Hooks;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Container\Container;

/**
 * Class being included by TCEmain using a hook
 */
class Tcemain {

	/**
	 * Fields to check for changes
	 *
	 * @var array
	 */
	protected $watchedFields = array(
		'approved',
		'spam'
	);

	/**
	 * notificationService
	 *
	 * @var \TYPO3\T3extblog\Service\CommentNotificationService
	 */
	protected $notificationService = NULL;

	/**
	 * objectContainer
	 *
	 * @var Container
	 */
	protected $objectContainer = NULL;

	/**
	 * commentRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\CommentRepository
	 */
	protected $commentRepository = NULL;

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
		if ($command === 'delete') {
			if ($table === 'tx_t3blog_post') {
				$this->deletePostRelations(intval($id));
			}
		}
	}

	/**
	 * TCEmain hook function for on-the-fly email sending
	 * Hook: processDatamap_afterDatabaseOperations
	 *
	 * @param string $status Status of the current operation, 'new' or 'update'
	 * @param string $table The table currently processing data for
	 * @param string $id The record uid currently processing data for
	 * @param array $fields The field array of a record
	 * @param DataHandler $tceMain
	 *
	 * @return void
	 */
	public function processDatamap_afterDatabaseOperations($status, $table, $id, $fields, $tceMain) {
		$pid = $tceMain->checkValue_currentRecord['pid'];
		if (!is_numeric($id)) {
			$id = $tceMain->substNEWwithIDs[$id];
		}

		// @todo Remove this when 6.2 is no longer relevant
		if (version_compare(TYPO3_branch, '7.2', '>=')) {
			if ($table === 'tx_t3blog_post') {
				if (isset($GLOBALS['_POST']['_savedokview_x'])) {
					$this->processPreview($id);
				}
			}
		}

		if ($table === 'tx_t3blog_com') {
			if ($status == 'update') {
				if ($this->isUpdateNeeded($fields, $this->watchedFields)) {
					$this->processChangedComment($id, $pid);
				}
			}

			if ($status === 'new') {
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

		/* @var $tce \TYPO3\CMS\Core\DataHandling\DataHandler */
		$tceMain = $this->getObjectContainer()->getInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');

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

		$where = $fieldName . '=' . $postId . BackendUtility::deleteClause($tableName) . $extraWhere;

		$data = $this->getDatabase()->exec_SELECTgetRows('uid', $tableName, $where);
		foreach ($data as $record) {
			$command[$record['uid']]['delete'] = 1;
		}

		return $command;
	}

	/**
	 * @param integer $id
	 */
	protected function processPreview($id) {
		$pagesTsConfig = BackendUtility::getPagesTSconfig($GLOBALS['_POST']['popViewId']);

		if (intval($pagesTsConfig['tx_t3extblog.']['singlePid'])) {
			$record = BackendUtility::getRecord('tx_t3blog_post', $id);
			$previewPageId = (int)$pagesTsConfig['tx_t3extblog.']['singlePid'];

			$parameters = array(
				'tx_t3extblog_blogsystem[controller]' => 'Post',
				'tx_t3extblog_blogsystem[action]' => 'preview',
				'tx_t3extblog_blogsystem[previewPost]' => $record['uid'],
				'no_cache' => 1,
			);
			if ($record['sys_language_uid'] > 0) {
				if ($record['l18n_parent'] > 0) {
					$parameters['tx_t3extblog_blogsystem[previewPost]'] = $record['l18n_parent'];
				}
				$parameters['L'] = $record['sys_language_uid'];
			}

			$previewDomainRootline = BackendUtility::BEgetRootLine($previewPageId);
			$previewDomain = BackendUtility::getViewDomain($previewPageId, $previewDomainRootline);
			$queryString = GeneralUtility::implodeArrayForUrl('', $parameters, '', FALSE, TRUE);

			$GLOBALS['_POST']['viewUrl'] = $previewDomain . '/index.php?id=' . $previewPageId . $queryString;
			$GLOBALS['_POST']['popViewId_addParams'] = $queryString;
			$GLOBALS['_POST']['popViewId'] = $previewPageId;
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
			->getInstance('TYPO3\\T3extblog\\Service\\SettingsService')
			->setPageUid($pid);

		$this->getNotificationService()->processNewEntity($this->getComment($id));
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
			->getInstance('TYPO3\\T3extblog\\Service\\SettingsService')
			->setPageUid($pid);

		$this->getNotificationService()->processChangedStatus($this->getComment($id));
	}

	/**
	 * Get comment
	 *
	 * @param integer $uid Page uid
	 *
	 * @return \TYPO3\T3extblog\Domain\Model\Comment
	 */
	protected function getComment($uid) {
		$comment = $this->getCommentRepository()->findByUid($uid);

		return $comment;
	}

	/**
	 * Get comment repository
	 *
	 * @return \TYPO3\T3extblog\Domain\Repository\CommentRepository
	 */
	protected function getCommentRepository() {
		if ($this->commentRepository === NULL) {
			$this->commentRepository = $this->getObjectContainer()->getInstance(
				'TYPO3\\T3extblog\\Domain\\Repository\\CommentRepository'
			);
		}

		return $this->commentRepository;
	}

	/**
	 * Get object container
	 *
	 * @return Container
	 */
	protected function getObjectContainer() {
		if ($this->objectContainer === NULL) {
			$this->objectContainer = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\Container\\Container');
		}

		return $this->objectContainer;
	}

	/**
	 * Get notification service
	 *
	 * @return \TYPO3\T3extblog\Service\CommentNotificationService
	 */
	protected function getNotificationService() {
		if ($this->notificationService === NULL) {
			$this->notificationService = $this->getObjectContainer()->getInstance(
				'TYPO3\\T3extblog\\Service\\CommentNotificationService'
			);
		}

		return $this->notificationService;
	}

	/**
	 * Check if one of our watched fields have been changed
	 *
	 * @param array $fields
	 * @param array $watchedFields
	 *
	 * @return bool
	 */
	protected function isUpdateNeeded($fields, $watchedFields) {
		// If uid field exists (and therefore all fields) nothing has been updated
		if (array_key_exists('uid', $fields)) {
			return FALSE;
		}

		// Check if one of the updated fields is relevant for us
		$changedFields = array_keys($fields);
		if (is_array($changedFields)) {
			$updatedFields = array_intersect($changedFields, $watchedFields);

			if (is_array($updatedFields) && count($updatedFields) > 0) {
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Get database connection
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabase() {
		return $GLOBALS['TYPO3_DB'];
	}

}
