<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\PageTitle\RecordTitleProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * ViewHelper to render the page title.
 */
class TitleTagViewHelper extends AbstractViewHelper
{
    public function __construct(protected readonly RecordTitleProvider $titleProvider)
    {
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('prepend', 'bool', 'Prepend to the existing page path title', false, true);
    }

    /**
     * Override the title tag.
     */
    public function render(): void
    {
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            return;
        }

        $prepend = $this->arguments['prepend'];
        $content = $this->renderChildren();

        if (!empty($content)) {
            if ($prepend === true) {
                $content .= $this->titleProvider->getTitle();
            } else {
                $content = $this->titleProvider->getTitle().$content;
            }

            $this->titleProvider->setTitle($content);
        }
    }
}
