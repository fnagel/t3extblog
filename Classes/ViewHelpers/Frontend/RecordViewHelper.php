<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Domain\RecordFactory;
use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper for creating a record object of a given entity.
 */
class RecordViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'entity',
            DomainObjectInterface::class,
            'Entity for which a record should be created for.',
            true
        );
    }

    public function render(): RecordInterface
    {
        /* @var $entity AbstractEntity */
        $entity = $this->arguments['entity'];

        return static::getRecord($entity);
    }

    public static function getRecord(DomainObjectInterface $entity): RecordInterface
    {
        $table = static::getTableName($entity);

        return GeneralUtility::makeInstance(RecordFactory::class)->createResolvedRecordFromDatabaseRow(
            $table,
            BackendUtility::getRecord($table, $entity->getUid())
        );
    }

    protected static function getTableName(DomainObjectInterface $entity): string
    {
        return GeneralUtility::makeInstance(DataMapper::class)
            ->getDataMap($entity::class)
            ->getTableName();
    }
}
