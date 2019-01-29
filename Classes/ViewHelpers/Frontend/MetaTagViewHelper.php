<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to render meta tags.
 *
 * Taken form Georg Ringer EXT:news but modified to use the nee SEO API (since TYPO3 9.x).
 * Only supports meta tags implemented in your TYPO3 instance. For more info, see:
 * https://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/MetaTagApi/Index.html
 * and the core extension "seo" (see EXT:seo/Classes/MetaTag/)
 */
class MetaTagViewHelper extends AbstractViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'meta';

    /**
     * Arguments initialization.
     */
    public function initializeArguments()
    {
        $this->registerArgument('property', 'string', 'Property of meta tag');
        $this->registerArgument('name', 'string', 'Content of meta tag using the name attribute');
        $this->registerArgument('content', 'string', 'Content of meta tag');
        $this->registerArgument('useCurrentDomain', 'bool', 'If set, current domain is used');
        $this->registerArgument('forceAbsoluteUrl', 'bool', 'If set, absolute url is forced');
    }

    /**
     * Renders a meta tag.
     *
     * @inheritdoc
     */
    public function render()
    {
        $property = $this->arguments['property'] ?? $this->arguments['name'];
        $content = $this->arguments['content'];
        $useCurrentDomain = $this->arguments['useCurrentDomain'];
        $forceAbsoluteUrl = $this->arguments['forceAbsoluteUrl'];

        // Set current domain
        if ($useCurrentDomain) {
            $content = GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL');
        }

        // Prepend current domain
        if ($forceAbsoluteUrl) {
            $siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');

            if (!GeneralUtility::isFirstPartOfStr($content, $siteUrl)) {
                $content = rtrim($siteUrl, '/') . '/' . ltrim($this->arguments['content'], '/');
            }
        }

        if (!empty($content)) {
            $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)->getManagerForProperty($property);
            $metaTagManager->addProperty($property, $content);
        }
    }
}
