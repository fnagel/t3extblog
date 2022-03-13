<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\FrontendUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to render <head> data
 */
class HeaderDataViewHelper extends AbstractViewHelper
{
    /**
     * Renders HeaderData
     *
     */
    public function render(): void
    {
        FrontendUtility::getPageRenderer()->addHeaderData($this->renderChildren());
    }
}
