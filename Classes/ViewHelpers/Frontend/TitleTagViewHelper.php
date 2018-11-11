<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Georg Ringer <typo3@ringerge.org>
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use FelixNagel\T3extblog\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

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
        $this->registerArgument('prepend', 'string', 'Uid of the backend user');
        $this->registerArgument('searchTitle', 'int', 'Width of the avatar image');
    }

    /**
     * Override the title tag.
     *
     * @todo Make use of new SEO API
     *
     * @inheritdoc
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $prepend = $arguments['prepend'];
        $searchTitle = $arguments['searchTitle'];

        if (TYPO3_MODE === 'BE') {
            return;
        }

        $content = $renderChildrenClosure();

        if (empty($content) !== true) {
            if ($prepend === true) {
                $content = $content.GeneralUtility::getTsFe()->page['title'];
            }

            if ($searchTitle === null) {
                $searchTitle = $content;
            }

            GeneralUtility::getTsFe()->indexedDocTitle = $searchTitle;
            GeneralUtility::getTsFe()->page['title'] = $content;
        }
    }
}
