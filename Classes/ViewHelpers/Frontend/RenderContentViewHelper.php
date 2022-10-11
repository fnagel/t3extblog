<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\FrontendUtility;
use TYPO3\CMS\Extbase\DomainObject\DomainObjectInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
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

    public function initializeArguments()
    {
        $this->registerArgument('contentElements', 'array', 'Content elements to render, array or object implementing \ArrayAccess to iterated over', true);
        $this->registerArgument('index', 'int', 'Index of array or object storage', false, 0);
        $this->registerArgument('table', 'string', 'Table to render', false, 'tt_content');
    }

    /**
     * Render content.
     */
    public function render(): string
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
     */
    protected function renderRecord(int $uid, string $table): string
    {
        if (empty(FrontendUtility::getTsFe()->recordRegister[$table.':'.$uid])) {
            return '';
        }

        $configuration = [
            'tables' => $table,
            'source' => $uid,
            'dontCheckPid' => 1,
        ];

        $parent = FrontendUtility::getTsFe()->currentRecord;
        if (!empty($parent)) {
            ++FrontendUtility::getTsFe()->recordRegister[$parent];
        }

        $html = $this->getContentObjectRenderer()->cObjGetSingle('RECORDS', $configuration);

        FrontendUtility::getTsFe()->currentRecord = $parent;
        if (!empty($parent)) {
            --FrontendUtility::getTsFe()->recordRegister[$parent];
        }

        return $html;
    }

    /**
     * Get element uid.
     *
     * @param array|DomainObjectInterface $element
     */
    protected function getElementUid($element): ?int
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
     */
    protected function getContentObjectRenderer(): ContentObjectRenderer
    {
        return FrontendUtility::getTsFe()->cObj;
    }
}
