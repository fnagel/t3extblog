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
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    public static function getTsFe()
    {
        if (TYPO3_MODE === 'BE') {
            throw new Exception('TSFE is not available in backend context!', 1582672848);
        }

        return $GLOBALS['TSFE'];
    }

    /**
     * Get FE language UID.
     *
     * @return int
     */
    public static function getLanguageUid()
    {
        $languageAspect = CoreGeneralUtility::makeInstance(Context::class)->getAspect('language');

        return $languageAspect->getId();
    }

    /**
     * CAUTION: disables whole FE cache!
     *
     * @return void
     */
    public static function disableFrontendCache()
    {
        if (isset($GLOBALS['TSFE'])) {
            $GLOBALS['TSFE']->no_cache = true;
        }
    }

    /**
     * Get page renderer.
     *
     * @return PageRenderer
     */
    public static function getPageRenderer()
    {
        return CoreGeneralUtility::makeInstance(PageRenderer::class);
    }

    /**
     * Check if FE user is logged in
     *
     * @return bool
     */
    public static function isUserLoggedIn()
    {
        $context = CoreGeneralUtility::makeInstance(Context::class);

        return (bool)$context->getPropertyFromAspect('frontend.user', 'isLoggedIn');
    }

    /**
     * Check if a valid BE login exists.
     *
     * $GLOBALS['TSFE']->isBackendUserLoggedIn() (and TS equivalent) does not work.
     * See https://forge.typo3.org/issues/23625
     *
     * @todo Workaround for bug in TYPO3
     * @todo Since t3extblog v5: Check if this is still needed
     *
     * @return bool
     */
    public static function isValidBackendUser()
    {
        // Init if needed
        if (!isset($GLOBALS['BE_USER'])) {
            Bootstrap::initializeBackendUser();
        }
        
        // Check for valid user
        return is_object($GLOBALS['BE_USER']) && !empty($GLOBALS['BE_USER']->user['uid']);
    }
}
