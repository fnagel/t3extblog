<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper PHP wordwrap.
 */
class WordwrapViewHelper extends AbstractViewHelper
{
    /**
     * @inheritDoc
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('width', 'int', 'Max characters per line', false, 75);
    }

    public function render()
    {
        $width = $this->arguments['width'];
        $content = $this->renderChildren();

        return wordwrap($content, $width);
    }
}
