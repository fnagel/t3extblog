<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use FelixNagel\T3extblog\Domain\Model\AbstractLocalizedEntity;

/**
 * ViewHelper for rendering content.
 */
class RenderContentViewHelper extends AbstractViewHelper
{
    /**
     * This view helper renders HTML, thus output must not be escaped
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * {@inheritdoc}
     */
    public function initializeArguments()
    {
        $this->registerArgument('contentElements', 'array', 'Content elements to render, array or object implementing \ArrayAccess to iterated over', true);
        $this->registerArgument('index', 'int', 'Index of array or object storage', false, 0);
        $this->registerArgument('table', 'string', 'Table to render', false, 'tt_content');
    }

    /**
     * Render content.
     *
     * @return string
     */
    public function render()
    {
        $output = '';
        $iterator = 0;

        foreach ($this->arguments['contentElements'] as $content) {
            ++$iterator;
            if (($iterator - 1) < $this->arguments['index']) {
                continue;
            }

            $uid = $this->getElementUid($content);
            if ($uid === null) {
                continue;
            }

            $output .= $this->renderRecord($uid, $this->arguments['table']);
        }

        return $output;
    }

    /**
     * This function renders a raw record into the corresponding
     * element by typoscript RENDER function.
     *
     * Taken from EXT:vhs/Classes/ViewHelpers/Content/AbstractContentViewHelper.php
     *
     * @param int    $uid
     * @param string $table
     *
     * @return string
     */
    protected function renderRecord($uid, $table)
    {
        if (0 < GeneralUtility::getTsFe()->recordRegister[$table.':'.$uid]) {
            return '';
        }

        $configuration = [
            'tables' => $table,
            'source' => $uid,
            'dontCheckPid' => 1,
        ];

        $parent = GeneralUtility::getTsFe()->currentRecord;
        if (!empty($parent)) {
            GeneralUtility::getTsFe()->recordRegister[$parent]++;
        }

        $html = $this->getContentObjectRenderer()->cObjGetSingle('RECORDS', $configuration);

        GeneralUtility::getTsFe()->currentRecord = $parent;
        if (!empty($parent)) {
            GeneralUtility::getTsFe()->recordRegister[$parent]--;
        }

        return $html;
    }

    /**
     * Get element uid.
     *
     * @param array|DomainObjectInterface $element
     *
     * @return int|null
     */
    protected function getElementUid($element)
    {
        if ($element instanceof DomainObjectInterface) {
            if ($element instanceof AbstractLocalizedEntity) {
                return (int) $element->getLocalizedUid();
            }

            return (int) $element->getUid();
        }

        if (is_array($element) && isset($element['uid'])) {
            return (int) $element['uid'];
        }

        return null;
    }

    /**
     * Get content object renderer.
     *
     * @return \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    protected function getContentObjectRenderer()
    {
        return GeneralUtility::getTsFe()->cObj;
    }
}
