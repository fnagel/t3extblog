<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;
;
/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * ViewHelper to render the page title.
 */
class TitleTagViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @inheritdoc
     */
    public function initializeArguments()
    {
        $this->registerArgument('prepend', 'string', 'Prepend to the existing page path title', false, true);
        $this->registerArgument('searchTitle', 'string', 'Title for search index', false, null);
    }

    /**
     * Override the title tag.
     *
     * @inheritdoc
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $prepend = $arguments['prepend'];
        $searchTitle = $arguments['searchTitle'];

        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            return;
        }

        $content = $renderChildrenClosure();

        if (!empty($content)) {
            if ($prepend === true) {
                $content .= GeneralUtility::getTsFe()->page['title'];
            }

            if ($searchTitle === null) {
                $searchTitle = $content;
            }

            GeneralUtility::getTsFe()->indexedDocTitle = $searchTitle;
            GeneralUtility::getTsFe()->page['title'] = $content;
        }
    }
}
