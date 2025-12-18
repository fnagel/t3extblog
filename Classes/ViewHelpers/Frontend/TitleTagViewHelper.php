<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Seo\PageTitleProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * ViewHelper to render the page title.
 */
class TitleTagViewHelper extends AbstractViewHelper
{
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
            $titleProvider = GeneralUtility::makeInstance(PageTitleProvider::class);

            if ($prepend === true) {
                $content .= $titleProvider->getTitle();
            } else {
                $content = $titleProvider->getTitle().$content;
            }

            $titleProvider->setTitle($content);
        }
    }
}
