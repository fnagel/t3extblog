<?php

namespace FelixNagel\T3extblog\Utility;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Exception\Exception;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility as CoreGeneralUtility;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * General utility class.
 *
 * @todo Rename to FrontendUtility
 */
class GeneralUtility implements SingletonInterface
{
    /**
     * Get TypoScript frontend controller.
     *
     */
    public static function getTsFe(): \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
    {
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isBackend()) {
            throw new Exception('TSFE is not available in backend context!', 1582672848);
        }

        return $GLOBALS['TSFE'];
    }

    /**
     * Get FE language UID.
     *
     */
    public static function getLanguageUid(): int
    {
        $languageAspect = CoreGeneralUtility::makeInstance(Context::class)->getAspect('language');

        return $languageAspect->getId();
    }

    /**
     * CAUTION: disables whole FE cache!
     *
     */
    public static function disableFrontendCache(): void
    {
        if (isset($GLOBALS['TSFE'])) {
            $GLOBALS['TSFE']->no_cache = true;
        }
    }

    /**
     * Get page renderer.
     *
     */
    public static function getPageRenderer(): PageRenderer
    {
        return CoreGeneralUtility::makeInstance(PageRenderer::class);
    }

    /**
     * Check if FE user is logged in
     *
     */
    public static function isUserLoggedIn(): bool
    {
        $context = CoreGeneralUtility::makeInstance(Context::class);

        return (bool)$context->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }

    /**
     * Check if a valid BE login exists.
     */
    public static function isValidBackendUser(): bool
    {
        return static::getTsFe()->isBackendUserLoggedIn();
    }
}
