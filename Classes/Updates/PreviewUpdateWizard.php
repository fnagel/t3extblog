<?php

namespace FelixNagel\T3extblog\Updates;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015-2018 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * FAL Update Wizard.
 */
class PreviewUpdateWizard extends AbstractUpdate
{
    /**
     * The database connection.
     *
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * @var array
     */
    protected $databaseQueries = [];

    /**
     * @var string
     */
    protected $title = 'Migrate ###MORE### marker within T3extblog post content elements.';

    /**
     * @var \TYPO3\CMS\Core\Resource\FileRepository
     */
    protected $fileRepository = null;

    /**
     * Construct class.
     */
    public function __construct()
    {
        $this->connectionPool =  GeneralUtility::makeInstance(ConnectionPool::class);
    }

    /**
     * Checks if an update is needed.
     *
     * @param string &$description : The description for the update
     *
     * @return bool TRUE if an update is needed, FALSE otherwise
     */
    public function checkForUpdate(&$description)
    {
        if ($this->isWizardDone() || !$this->checkIfTableExists('tx_t3blog_post')) {
            return false;
        }

        $updateNeeded = false;
        $notMigratedRows = $this->getPostsWithContentElementsAndWithoutPreview();

        foreach ($notMigratedRows as $key => $record) {
            $contentElementsWithText = $this->getAllTextPicContentElementsByPost($record['uid']);

            // Remove posts with content elements without a MORE marker
            $allBodyText = '';
            foreach ($contentElementsWithText as $content) {
                $allBodyText .= $content['bodytext'];
            }
            if (strpos($allBodyText, '###MORE###') === false) {
                unset($notMigratedRows[$key]);
            }
        }

        if (count($notMigratedRows) > 0) {
            $updateNeeded = true;
            $description = 'There are '.count($notMigratedRows).' post records with '.
                'a ###MORE### marker within the related content elements. This Wizard will remove this marker and '.
                'use the prefixing text as new preview text. When a textpic or image content element '.
                'has been found before the marker, the first of its images will be used as new preview image.';
        }

        return $updateNeeded;
    }

    /**
     * Performs the database update.
     *
     * @param array &$dbQueries      Queries done in this update
     * @param mixed &$customMessages Custom messages
     *
     * @return bool TRUE on success, FALSE on error
     */
    public function performUpdate(array &$dbQueries, &$customMessages)
    {
        $notMigratedRows = $this->getPostsWithContentElementsAndWithoutPreview();

        foreach ($notMigratedRows as $key => $record) {
            $contentElementsWithText = $this->getAllTextPicContentElementsByPost($record['uid']);

            if (!$this->migrateRecord($record, $contentElementsWithText)) {
                unset($notMigratedRows[$key]);
            }
        }

        $customMessages = count($notMigratedRows).' posts with ###MORE### marker have been migrated.';
        $dbQueries = $this->databaseQueries;

        $this->markWizardAsDone();

        return true;
    }

    /**
     * Migrate content elements and update post record.
     *
     * @param array $postRecord
     * @param array $contentElements
     *
     * @return bool
     */
    public function migrateRecord($postRecord, $contentElements)
    {
        $textBeforeDivider = '';
        $firstImageElementBeforeDivider = null;
        $hasMarker = false;

        foreach ($contentElements as $content) {
            $dividerPosition = strpos($content['bodytext'], '###MORE###');

            // Determine first image before divider
            if (
                $firstImageElementBeforeDivider === null &&
                ($content['CType'] !== 'text' && $content['image'] > 0)
            ) {
                $firstImageElementBeforeDivider = $content;
            }

            if ($dividerPosition !== false) {
                $hasMarker = true;
                $textBeforeDivider .= strstr($content['bodytext'], '###MORE###', true);

                $this->updatePostRecord($postRecord, $textBeforeDivider, $firstImageElementBeforeDivider);
                $this->updateContentRecord($content);

                break;
            }

            $textBeforeDivider .= $content['bodytext'];
        }

        return $hasMarker;
    }

    /**
     * Update tt_content row.
     *
     * @param array $contentRecord
     */
    protected function updateContentRecord($contentRecord)
    {
        $table = 'tt_content';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder
            ->update($table)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter((int) $contentRecord['uid'], \PDO::PARAM_INT)
                )
            )
            ->set('bodytext', str_replace('###MORE###', '', $contentRecord['bodytext']))
            ->execute();

        $this->logDatabaseExec($queryBuilder->getSql());
    }

    /**
     * Update records.
     *
     * @param array  $postRecord
     * @param string $textBeforeDivider
     * @param array  $firstImageRecord
     */
    protected function updatePostRecord($postRecord, $textBeforeDivider, $firstImageRecord)
    {
        $hasPreviewImage = false;
        if ($firstImageRecord !== null) {
            $fileObjectArray = $this->getFileRepository()->findByRelation('tt_content', 'image', $firstImageRecord['uid']);

            if (is_array($fileObjectArray) && count($fileObjectArray) > 0) {
                // Get the first file
                $fileObject = reset($fileObjectArray);
                $hasPreviewImage = true;

                $this->createPostSysFileReference($fileObject, $postRecord);
            }
        }

        // Update post
        $table = 'tx_t3blog_post';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder
            ->update($table)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter((int) $postRecord['uid'], \PDO::PARAM_INT)
                ),
                $this->getNonPreviewWhereClause($queryBuilder)
            );

        $queryBuilder->set('preview_text', $textBeforeDivider);
        if ($hasPreviewImage === true) {
            $queryBuilder->set('preview_image', 1);
        }

        $queryBuilder->execute();

        $this->logDatabaseExec($queryBuilder->getSql());
    }

    /**
     * Add new sys_file_reference for the preview image.
     *
     * @param FileReference $fileObject
     * @param array         $postRecord
     */
    protected function createPostSysFileReference(FileReference $fileObject, $postRecord)
    {
        if (!$fileObject instanceof FileReference) {
            return;
        }

        $dataArray = [
            'uid_local' => $fileObject->getOriginalFile()->getUid(),
            'tablenames' => 'tx_t3blog_post',
            'fieldname' => 'preview_image',
            'uid_foreign' => $postRecord['uid'],
            'table_local' => 'sys_file',
            'cruser_id' => 999,
            // the sys_file_reference record should always placed on the same page
            // as the record to link to, see issue #46497
            'pid' => $postRecord['pid'],
        ];

        $table = 'sys_file_reference';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder
            ->insert($table)
            ->values($dataArray)
            ->execute();

        $this->logDatabaseExec($queryBuilder->getSql());
    }

    /**
     * Gets post content with text.
     *
     * @param int $postUid
     *
     * @return array
     */
    protected function getAllTextPicContentElementsByPost($postUid)
    {
        $table = 'tt_content';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('uid', 'bodytext', 'CType', 'image')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq(
                    'irre_parentid',
                    $queryBuilder->createNamedParameter((int) $postUid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'irre_parenttable',
                    $queryBuilder->createNamedParameter('tx_t3blog_post')
                ),
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('text')),
                    $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('textpic')),
                    $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('image'))
                )
            )
            ->orderBy('sorting');

        $rows = $queryBuilder->execute()->fetchAll();

        $this->logDatabaseExec($queryBuilder->getSql());

        return $rows;
    }

    /**
     * Gets all posts with content element.
     *
     * @return array
     */
    protected function getPostsWithContentElementsAndWithoutPreview()
    {
        $table = 'tx_t3blog_post';
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('uid', 'pid', 'content')
            ->from($table)
            ->where(
                $queryBuilder->expr()->gt('content', 0),
                $this->getNonPreviewWhereClause($queryBuilder)
            );

        $rows = $queryBuilder->execute()->fetchAll();

        $this->logDatabaseExec($queryBuilder->getSql());

        return $rows;
    }

    /**
     * Gets where clause for old posts (= without preview image or text).
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return string
     */
    protected function getNonPreviewWhereClause(QueryBuilder $queryBuilder)
    {
        return $queryBuilder->expr()->andX(
            $queryBuilder->expr()->isNull('preview_text'),
            $queryBuilder->expr()->eq(
                'preview_image',
                $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)
            )
        );
    }

    /**
     * Log DB usage.
     *
     * @return FileRepository
     */
    protected function getFileRepository()
    {
        if ($this->fileRepository === null) {
            $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        }

        return $this->fileRepository;
    }

    /**
     * Log DB usage.
     *
     * @param $string
     */
    protected function logDatabaseExec($string)
    {
        $this->databaseQueries[] = $string;
    }
}
