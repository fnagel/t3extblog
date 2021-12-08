<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;
use TYPO3\CMS\Extbase\Domain\Model\BackendUser;

/**
 * Views sprite icon for a record (object).
 */
class SpriteIconForRecordViewHelper extends AbstractBackendViewHelper
{
    /**
     * This view helper renders HTML, thus output must not be escaped
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @inheritdoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('table', 'string', 'Database table', true);
        $this->registerArgument('object', 'object', 'Object', true);
    }

    /**
     * Displays spriteIcon for database table and object.
     *
     *
     * @see t3lib_iconWorks::getSpriteIconForRecord($table, $row)
     */
    public function render(): string
    {
        $table = $this->arguments['table'];
        $object = $this->arguments['object'];

        if (!is_object($object) || !method_exists($object, 'getUid')) {
            return '';
        }

        $row = [
            'uid' => $object->getUid(),
            'startTime' => false,
            'endTime' => false,
        ];

        if (method_exists($object, 'getIsDisabled')) {
            $row['disable'] = $object->getIsDisabled();
        }

        if ($table === 'be_users' && $object instanceof BackendUser) {
            $row['admin'] = $object->getIsAdministrator();
        }

        if (method_exists($object, 'getStartDateAndTime')) {
            $row['startTime'] = $object->getStartDateAndTime();
        }

        if (method_exists($object, 'getEndDateAndTime')) {
            $row['endTime'] = $object->getEndDateAndTime();
        }

        /* @var $iconFactory \TYPO3\CMS\Core\Imaging\IconFactory */
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        return $iconFactory->getIconForRecord($table, $row, Icon::SIZE_SMALL)->render();
    }
}
