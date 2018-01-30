<?php

namespace TYPO3\T3extblog\Hooks;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2016 Felix Nagel <info@felixnagel.com>
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
use TYPO3\T3extblog\Service\FlushCacheService;

/**
 * Class being included by TCEmain using a hook.
 */
class Tcemain
{
    /**
     * Fields to check for changes.
     *
     * @var array
     */
    protected $watchedFields = array(
        'approved',
        'spam',
    );

    /**
     * notificationService.
     *
     * @var \TYPO3\T3extblog\Service\CommentNotificationService
     */
    protected $notificationService = null;

    /**
     * objectContainer.
     *
     * @var Container
     */
    protected $objectContainer = null;

    /**
     * commentRepository.
     *
     * @var \TYPO3\T3extblog\Domain\Repository\CommentRepository
     */
    protected $commentRepository = null;

    /**
     * Before processing: clear cache.
     *
     * @param string      $command            The TCEmain operation status, fx. 'update'
     * @param string      $table              The table TCEmain is currently processing
     * @param string      $id                 The records id (if any)
     * @param array       $relativeTo         Filled if command is relative to another element
     * @param DataHandler $tceMain            Reference to the parent object (TCEmain)
     * @param bool        $commandIsProcessed If the command has been processed
     */
    public function processCmdmap($command, $table, $id, $relativeTo, $commandIsProcessed, $tceMain)
    {
        if (!in_array($table, array('tx_t3blog_post', 'tx_t3blog_com'))) {
            return;
        }

        if ($command === 'delete') {
            $pid = $this->resolveRecordPid($table, $id, $tceMain);
            $tagsToFlush = array();

            // Cache tags
            if ($table === 'tx_t3blog_post') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
                $tagsToFlush[] = $table.'_uid_'.$id;
            }
            if ($table === 'tx_t3blog_com') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
            }

            FlushCacheService::flushFrontendCacheByTags($tagsToFlush);
        }
    }

    /**
     * After processing: delete related objects.
     *
     * @param string      $command    The TCEmain operation status, fx. 'update'
     * @param string      $table      The table TCEmain is currently processing
     * @param string      $id         The records id (if any)
     * @param array       $relativeTo Filled if command is relative to another element
     * @param DataHandler $tceMain    Reference to the parent object (TCEmain)
     */
    public function processCmdmap_postProcess($command, $table, $id, $relativeTo, $tceMain)
    {
        if ($command === 'delete') {
            if ($table === 'tx_t3blog_post') {
                $this->deletePostRelations(intval($id));
            }
        }
    }

    /**
     * TCEmain hook function for on-the-fly email sending
     * Hook: processDatamap_afterDatabaseOperations.
     *
     * @param string      $status  The command which has been sent to processDatamap
     * @param string      $table   The table we're dealing with
     * @param mixed       $id      Either the record UID or a string if a new record has been created
     * @param array       $fields  The record row how it has been inserted into the database
     * @param DataHandler $tceMain A reference to the TCEmain instance
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, $fields, $tceMain)
    {
        if (!in_array($table, array('tx_t3blog_post', 'tx_t3blog_com', 'tx_t3blog_cat'))) {
            return;
        }

        $pid = $this->resolveRecordPid($table, $id, $tceMain, $fields);
        $id = $this->resolveRecordUid($id, $tceMain);

        $tagsToFlush = array();

        if ($table === 'tx_t3blog_post') {
            // @todo Remove this when 6.2 is no longer relevant
            if (version_compare(TYPO3_branch, '7.2', '<=')) {
                if (isset($GLOBALS['_POST']['_savedokview_x'])) {
                    $this->processPreview($id);
                }
            }

            // Cache tags for posts
            if ($status == 'update' || $status === 'new') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
                $tagsToFlush[] = $table.'_uid_'.$id;
            }
        }

        if ($table === 'tx_t3blog_com') {
            if ($status == 'update') {
                if ($this->isUpdateNeeded($fields, $this->watchedFields)) {
                    $this->processChangedComment($id, $pid);
                }
            }

            if ($status === 'new') {
                $this->processNewComment($id, $pid);
            }

            // Cache tags for comments
            if ($status == 'update' || $status === 'new') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
            }
        }

        if ($table === 'tx_t3blog_cat') {
            // Cache tags for categories
            if ($status == 'update' || $status === 'new') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
            }
        }

        FlushCacheService::flushFrontendCacheByTags($tagsToFlush);
    }

    /**
     * Deletes all data associated with the post when post is deleted.
     *
     * @param int $id
     */
    protected function deletePostRelations($id)
    {
        $command = array(
            'tx_t3blog_com' => $this->getDeleteArrayForTable($id, 'tx_t3blog_com', 'fk_post'),
            'tx_t3blog_com_nl' => $this->getDeleteArrayForTable($id, 'tx_t3blog_com_nl', 'post_uid'),
            'tx_t3blog_trackback' => $this->getDeleteArrayForTable($id, 'tx_t3blog_trackback', 'postid'),
            'tt_content' => $this->getDeleteArrayForTable($id, 'tt_content', 'irre_parentid', ' AND irre_parenttable=\'tx_t3blog_post\''),
        );

        /* @var $tce \TYPO3\CMS\Core\DataHandling\DataHandler */
        $tceMain = $this->getObjectContainer()->getInstance('TYPO3\\CMS\\Core\\DataHandling\\DataHandler');

        $tceMain->start(array(), $command);
        $tceMain->process_cmdmap();
    }

    /**
     * @param int    $postId
     * @param string $tableName
     * @param string $fieldName
     * @param string $extraWhere
     *
     * @return array
     */
    protected function getDeleteArrayForTable($postId, $tableName, $fieldName, $extraWhere = '')
    {
        $command = array();
        $where = $fieldName.'='.$postId.BackendUtility::deleteClause($tableName).$extraWhere;

        $data = $this->getDatabase()->exec_SELECTgetRows('uid', $tableName, $where);
        foreach ($data as $record) {
            $command[$record['uid']]['delete'] = 1;
        }

        return $command;
    }

    /**
     * @param int $id
     */
    protected function processPreview($id)
    {
        $pagesTsConfig = BackendUtility::getPagesTSconfig($GLOBALS['_POST']['popViewId']);

        if (intval($pagesTsConfig['tx_t3extblog.']['singlePid'])) {
            $record = BackendUtility::getRecord('tx_t3blog_post', $id);
            $previewPageId = (int) $pagesTsConfig['tx_t3extblog.']['singlePid'];

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
            $queryString = GeneralUtility::implodeArrayForUrl('', $parameters, '', false, true);

            $GLOBALS['_POST']['viewUrl'] = $previewDomain.'/index.php?id='.$previewPageId.$queryString;
            $GLOBALS['_POST']['popViewId_addParams'] = $queryString;
            $GLOBALS['_POST']['popViewId'] = $previewPageId;
        }
    }

    /**
     * @param int $id
     * @param int $pid
     *
     * @internal param int $fields
     */
    protected function processNewComment($id, $pid)
    {
        $this->ensureCorrectTypoScriptSettings($pid);
        $this->getNotificationService()->processNewEntity($this->getComment($id));
    }

    /**
     * @param int $id
     * @param int $pid
     *
     * @internal param int $fields
     */
    protected function processChangedComment($id, $pid)
    {
        $this->ensureCorrectTypoScriptSettings($pid);
        $this->getNotificationService()->processChangedStatus($this->getComment($id));
    }

    /**
     * Get comment.
     *
     * @param int $uid Page uid
     *
     * @return \TYPO3\T3extblog\Domain\Model\Comment
     */
    protected function getComment($uid)
    {
        $comment = $this->getCommentRepository()->findByUid($uid);

        return $comment;
    }

    /**
     * Get comment repository.
     *
     * @return \TYPO3\T3extblog\Domain\Repository\CommentRepository
     */
    protected function getCommentRepository()
    {
        if ($this->commentRepository === null) {
            $this->commentRepository = $this->getObjectContainer()->getInstance(
                'TYPO3\\T3extblog\\Domain\\Repository\\CommentRepository'
            );
        }

        return $this->commentRepository;
    }

    /**
     * Get object container.
     *
     * @return Container
     */
    protected function getObjectContainer()
    {
        if ($this->objectContainer === null) {
            $this->objectContainer = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\Container\\Container');
        }

        return $this->objectContainer;
    }

    /**
     * Get notification service.
     *
     * @return \TYPO3\T3extblog\Service\CommentNotificationService
     */
    protected function getNotificationService()
    {
        if ($this->notificationService === null) {
            $this->notificationService = $this->getObjectContainer()->getInstance(
                'TYPO3\\T3extblog\\Service\\CommentNotificationService'
            );
        }

        return $this->notificationService;
    }

    /**
     * Get record uid.
     *
     * @param int         $id
     * @param DataHandler $reference
     *
     * @return int
     */
    protected function resolveRecordUid($id, DataHandler $reference)
    {
        if (false !== strpos($id, 'NEW')) {
            if (false === empty($reference->substNEWwithIDs[$id])) {
                $id = $reference->substNEWwithIDs[$id];
            }
        }

        return (int) $id;
    }
    /**
     * Get record pid.
     *
     * @param string      $table
     * @param int         $id
     * @param DataHandler $tceMain
     * @param array       $fields
     *
     * @return int
     */
    protected function resolveRecordPid($table, $id, $tceMain, $fields = null)
    {
        // Changed records
        if (isset($tceMain->checkValue_currentRecord['pid'])) {
            return (int) $tceMain->checkValue_currentRecord['pid'];
        }

        // New records
        if (is_array($fields) && isset($fields['pid'])) {
            return (int) $fields['pid'];
        }

        // Fallback (used for deleted records)
        list($pid) = BackendUtility::getTSCpid($table, $id, '');

        return (int) $pid;
    }

    /**
     * Check if one of our watched fields have been changed.
     *
     * @param array $fields
     * @param array $watchedFields
     *
     * @return bool
     */
    protected function isUpdateNeeded($fields, $watchedFields)
    {
        // If uid field exists (and therefore all fields) nothing has been updated
        if (array_key_exists('uid', $fields)) {
            return false;
        }

        // Check if one of the updated fields is relevant for us
        $changedFields = array_keys($fields);
        if (is_array($changedFields)) {
            $updatedFields = array_intersect($changedFields, $watchedFields);

            if (is_array($updatedFields) && count($updatedFields) > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get database connection.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabase()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Extbase TS fix.
     *
     * @param int $pid
     */
    protected function ensureCorrectTypoScriptSettings($pid)
    {
        $this->getObjectContainer()
            ->getInstance('TYPO3\\T3extblog\\Service\\SettingsService')
            ->setPageUid($pid);
    }
}
