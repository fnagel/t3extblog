<?php

namespace FelixNagel\T3extblog\Utility;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Cache\CacheInstruction;

/**
 * General utility class.
 */
class FrontendUtility implements SingletonInterface
{
    /**
     * Get FE page UID.
     */
    public static function getPageUid(): int
    {
        return static::getRequest()->getAttribute('frontend.page.information')->getId();
    }

    /**
     * Get FE language UID.
     */
    public static function getLanguageUid(): int
    {
        return static::getContext()->getAspect('language')->getId();
    }

    /**
     * CAUTION: disables whole FE cache!
     */
    public static function disableFrontendCache(): void
    {
        /* @var $cache CacheInstruction */
        $cache = static::getRequest()->getAttribute('frontend.cache.instruction');
        $cache->disableCache('EXT:t3extblog');
    }

    /**
     * Get page renderer.
     */
    public static function getPageRenderer(): PageRenderer
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }

    public static function getFrontendUser(): FrontendUserAuthentication
    {
        return static::getRequest()->getAttribute('frontend.user');
    }

    /**
     * Check if FE user is logged in
     */
    public static function isUserLoggedIn(): bool
    {
        return (bool)static::getContext()->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }

    /**
     * Check if a valid BE login exists.
     */
    public static function isValidBackendUser(): bool
    {
        return (bool)static::getContext()->getPropertyFromAspect('backend.user', 'isLoggedIn', false);
    }

    protected static function getContext(): Context
    {
        return GeneralUtility::makeInstance(Context::class);
    }

    protected static function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
