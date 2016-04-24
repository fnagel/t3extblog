<?php

namespace TYPO3\T3extblog\ViewHelpers\Frontend;

/**
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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper to render meta tags
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
class MetaTagViewHelper extends AbstractTagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'meta';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerTagAttribute('useCurrentDomain', 'bool', 'If set, current domain is used', FALSE, FALSE);
		$this->registerTagAttribute('forceAbsoluteUrl', 'bool', 'If set, absolute url is forced', FALSE, FALSE);
		$this->registerTagAttribute('property', 'string', 'Property of meta tag');
		$this->registerTagAttribute('name', 'string', 'Content of meta tag using the name attribute');
		$this->registerTagAttribute('content', 'string', 'Content of meta tag');
	}

	/**
	 * Renders a meta tag
	 *
	 * @return void
	 */
	public function render() {
		// set current domain
		if ($this->arguments['useCurrentDomain']) {
			$this->tag->addAttribute('content', GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));
		}

		// prepend current domain
		if ($this->arguments['forceAbsoluteUrl']) {
			$path = $this->arguments['content'];
			if (!GeneralUtility::isFirstPartOfStr($path, GeneralUtility::getIndpEnv('TYPO3_SITE_URL'))) {
				$this->tag->addAttribute(
					'content',
					rtrim(GeneralUtility::getIndpEnv('TYPO3_SITE_URL'), '/') . '/' . ltrim($this->arguments['content']), '/'
				);
			}
		}

		if (
			$this->arguments['useCurrentDomain'] ||
			(isset($this->arguments['content']) && !empty($this->arguments['content']))
		) {
			\TYPO3\T3extblog\Utility\GeneralUtility::getPageRenderer()->addMetaTag($this->tag->render());
		}
	}
}
