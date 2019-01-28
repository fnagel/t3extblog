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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper to render meta tags.
 *
 * Taken form Georg Ringer EXT:news
 *
 * # Example: Basic Example: News title as og:title meta tag
 * <code>
 * <n:metaTag property="og:title" content="{newsItem.title}" />
 * </code>
 * <output>
 * <meta property="og:title" content="TYPO3 is awesome" />
 * </output>
 *
 * # Example: Force the attribute "name"
 * <code>
 * <n:metaTag name="keywords" content="{newsItem.keywords}" />
 * </code>
 * <output>
 * <meta name="keywords" content="news 1, news 2" />
 * </output>
 */
class MetaTagViewHelper extends AbstractTagBasedViewHelper
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
        $this->registerTagAttribute('property', 'string', 'Property of meta tag');
        $this->registerTagAttribute('name', 'string', 'Content of meta tag using the name attribute');
        $this->registerTagAttribute('content', 'string', 'Content of meta tag');
        $this->registerArgument('useCurrentDomain', 'bool', 'If set, current domain is used');
        $this->registerArgument('forceAbsoluteUrl', 'bool', 'If set, absolute url is forced');
    }

    /**
     * Renders a meta tag.
     *
     * @todo Make use of new SEO API
     *
     * @inheritdoc
     */
    public function render()
    {
        $useCurrentDomain = $this->arguments['useCurrentDomain'];
        $forceAbsoluteUrl = $this->arguments['forceAbsoluteUrl'];

        // Set current domain
        if ($useCurrentDomain) {
            $this->tag->addAttribute('content', GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));
        }

        // Prepend current domain
        if ($forceAbsoluteUrl) {
            $path = $this->arguments['content'];
            $siteUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');

            if (!GeneralUtility::isFirstPartOfStr($path, $siteUrl)) {
                $this->tag->addAttribute(
                    'content',
                    rtrim($siteUrl, '/') . '/' . ltrim($this->arguments['content'], '/')
                );
            }
        }

        if ($useCurrentDomain || (isset($this->arguments['content']) && !empty($this->arguments['content']))) {
            \FelixNagel\T3extblog\Utility\GeneralUtility::getPageRenderer()->addMetaTag($this->tag->render());
        }
    }
}
