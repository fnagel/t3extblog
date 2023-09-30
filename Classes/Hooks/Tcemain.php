<?php

namespace FelixNagel\T3extblog\Hooks;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Service\CommentNotificationService;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use FelixNagel\T3extblog\Service\FlushCacheService;

/**
 * Class being included by TCEmain using a hook.
 */
class Tcemain
{
    /**
     * Fields to check for changes.
     */
    protected array $watchedFields = [
        'approved',
        'spam',
    ];

    /**
     * notificationService.
     */
    protected ?CommentNotificationService $notificationService = null;

    /**
     * commentRepository.
     */
    protected ?CommentRepository $commentRepository = null;

    /**
     * Before processing: clear cache.
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     *
     * @param string      $command            The TCEmain operation status, fx. 'update'
     * @param string      $table              The table TCEmain is currently processing
     * @param string      $id                 The records id (if any)
     * @param mixed       $value	          The value containing the data
     * @param bool        $commandIsProcessed If the command has been processed
     * @param DataHandler $tceMain            Reference to the parent object (TCEmain)
     */
    public function processCmdmap(
        string $command,
        string $table,
        string $id,
        mixed $value,
        bool $commandIsProcessed,
        DataHandler $tceMain
    ) {
        if (!in_array($table, ['tx_t3blog_post', 'tx_t3blog_com'])) {
            return;
        }

        if ($command === 'delete') {
            $pid = $this->resolveRecordPid($table, $id, $tceMain);
            $tagsToFlush = [];

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
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     *
     * @param string      $command    The TCEmain operation status, fx. 'update'
     * @param string      $table      The table TCEmain is currently processing
     * @param string      $id         The records id (if any)
     * @param mixed       $value	  The value containing the data
     * @param DataHandler $tceMain    Reference to the parent object (TCEmain)
     */
    public function processCmdmap_postProcess(
        string $command,
        string $table,
        string $id,
        mixed $value,
        DataHandler $tceMain
    ) {
        if ($command === 'delete' && $table === 'tx_t3blog_post') {
            $this->deletePostRelations((int) $id);
        }
    }

    /**
     * TCEmain hook function for on-the-fly email sending
     * Hook: processDatamap_afterDatabaseOperations.
     *
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
     *
     * @param string      $status  The command which has been sent to processDatamap
     * @param string      $table   The table we're dealing with
     * @param mixed       $id      Either the record UID or a string if a new record has been created
     * @param array       $fields  The record row how it has been inserted into the database
     * @param DataHandler $tceMain A reference to the TCEmain instance
     */
    public function processDatamap_afterDatabaseOperations(string $status, string $table, mixed $id, array $fields, DataHandler $tceMain)
    {
        if (!in_array($table, ['tx_t3blog_post', 'tx_t3blog_com', 'tx_t3blog_cat'])) {
            return;
        }

        $pid = $this->resolveRecordPid($table, $id, $tceMain, $fields);
        $id = $this->resolveRecordUid($id, $tceMain);

        $tagsToFlush = [];

        // Cache tags for posts
        if ($table === 'tx_t3blog_post' && ($status === 'update' || $status === 'new')) {
            $tagsToFlush[] = $table.'_pid_'.$pid;
            $tagsToFlush[] = $table.'_uid_'.$id;
        }

        if ($table === 'tx_t3blog_com') {
            if ($status === 'update' && $this->isUpdateNeeded($fields, $this->watchedFields)) {
                $this->processChangedComment($id);
            }

            if ($status === 'new') {
                $this->processNewComment($id);
            }

            // Cache tags for comments
            if ($status === 'update' || $status === 'new') {
                $tagsToFlush[] = $table.'_pid_'.$pid;
            }
        }

        // Cache tags for categories
        if ($table === 'tx_t3blog_cat' && ($status === 'update' || $status === 'new')) {
            $tagsToFlush[] = $table.'_pid_'.$pid;
        }

        FlushCacheService::flushFrontendCacheByTags($tagsToFlush);
    }

    /**
     * Deletes all data associated with the post when post is deleted.
     */
    protected function deletePostRelations(int $id)
    {
        $command = [
            'tx_t3blog_com' => $this->getDeleteArrayForTable($id, 'tx_t3blog_com', 'fk_post'),
            'tx_t3blog_com_nl' => $this->getDeleteArrayForTable($id, 'tx_t3blog_com_nl', 'post_uid'),
            'tx_t3blog_trackback' => $this->getDeleteArrayForTable($id, 'tx_t3blog_trackback', 'postid'),
            'tt_content' => $this->getDeleteArrayForTable($id, 'tt_content', 'irre_parentid'),
        ];

        /* @var $tceMain DataHandler */
        $tceMain = GeneralUtility::makeInstance(DataHandler::class);

        $tceMain->start([], $command);
        $tceMain->process_cmdmap();
    }

    protected function getDeleteArrayForTable(int $postId, string $tableName, string $fieldName): array
    {
        $command = [];

        $connectionPool =  GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable($tableName);
        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        $constraints = [
            $queryBuilder->expr()->eq($fieldName, $queryBuilder->createNamedParameter($postId, \PDO::PARAM_INT))
        ];

        if ($tableName === 'tt_content') {
            $constraints[] = $queryBuilder->expr()->eq(
                'irre_parenttable',
                $queryBuilder->createNamedParameter('tx_t3blog_post')
            );
        }

        $queryBuilder
            ->select('uid')
            ->from($tableName)
            ->where(CompositeExpression::and($constraints));

        $rows = $queryBuilder->execute()->fetchAll();

        foreach ($rows as $record) {
            $command[$record['uid']]['delete'] = 1;
        }

        return $command;
    }

    protected function processNewComment(int $id)
    {
        $this->getNotificationService()->processNewEntity($this->getComment($id));
    }

    protected function processChangedComment(int $id)
    {
        $this->getNotificationService()->processChangedStatus($this->getComment($id));
    }

    /**
     * Get comment.
     *
     * @param int $uid Page uid
     */
    protected function getComment(int $uid): Comment
    {
        /* @var $comment Comment */
        $comment = $this->getCommentRepository()->findByUid($uid);

        return $comment;
    }

    /**
     * Get comment repository.
     */
    protected function getCommentRepository(): CommentRepository
    {
        if ($this->commentRepository === null) {
            $this->commentRepository = GeneralUtility::makeInstance(CommentRepository::class);
        }

        return $this->commentRepository;
    }

    /**
     * Get notification service.
     */
    protected function getNotificationService(): CommentNotificationService
    {
        if ($this->notificationService === null) {
            $this->notificationService = GeneralUtility::makeInstance(CommentNotificationService::class);
        }

        return $this->notificationService;
    }

    /**
     * Get record uid.
     */
    protected function resolveRecordUid(int|string $id, DataHandler $reference): int
    {
        if (str_contains($id, 'NEW') && !empty($reference->substNEWwithIDs[$id])) {
            $id = $reference->substNEWwithIDs[$id];
        }

        return (int) $id;
    }

    /**
     * Get record pid.
     */
    protected function resolveRecordPid(string $table, int|string $id, DataHandler $tceMain, array $fields = null): int
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
        [$pid] = BackendUtility::getTSCpid($table, $id, '');

        return (int) $pid;
    }

    /**
     * Check if one of our watched fields have been changed.
     */
    protected function isUpdateNeeded(array $fields, array $watchedFields): bool
    {
        // If uid field exists (and therefore all fields) nothing has been updated
        if (array_key_exists('uid', $fields)) {
            return false;
        }

        // Check if one of the updated fields is relevant for us
        $changedFields = array_keys($fields);
        if (is_array($changedFields)) {
            $updatedFields = array_intersect($changedFields, $watchedFields);

            if (is_array($updatedFields) && $updatedFields !== []) {
                return true;
            }
        }

        return false;
    }
}
