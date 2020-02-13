<?php

namespace FelixNagel\T3extblog\Utility;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\GeneralUtility as CoreGeneralUtility;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * General utility class.
 *
 * @todo Rename to FrontendUtility
 */
class GeneralUtility implements SingletonInterface
{
    /**
     * @var array
     */
    protected static $tsFeCache = [];

    /**
     * Get TypoScript frontend controller.
     *
     * @param int $pageUid
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    public static function getTsFe($pageUid = 0)
    {
        if (TYPO3_MODE === 'BE') {
            $pageUid = (int) $pageUid;

            if (array_key_exists($pageUid, self::$tsFeCache)) {
                $GLOBALS['TSFE'] = self::$tsFeCache[$pageUid];
            } else {
                self::$tsFeCache[$pageUid] = self::generateTypoScriptFrontendController($pageUid);
            }
        }

        return $GLOBALS['TSFE'];
    }

    /**
     * Get FE meta charset.
     *
     * @param int $pageUid
     *
     * @return string
     */
    public static function getCharset($pageUid = 0)
    {
        return self::getTsFe($pageUid)->metaCharset;
    }

    /**
     * Generate TypoScriptFrontendController (use in BE context).
     *
     * @param int $pageUid
     * @param int $pageType
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected static function generateTypoScriptFrontendController($pageUid, $pageType = 0)
    {
        if (!is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = new TimeTracker();
            $GLOBALS['TT']->start();
        }

        $GLOBALS['TSFE'] = CoreGeneralUtility::makeInstance(
            'TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController',
            $GLOBALS['TYPO3_CONF_VARS'],
            (int) $pageUid,
            $pageType
        );

        $GLOBALS['TSFE']->connectToDB();
        $GLOBALS['TSFE']->initFEuser();
        $GLOBALS['TSFE']->determineId();
        $GLOBALS['TSFE']->initTemplate();
        $GLOBALS['TSFE']->getConfigArray();
        $GLOBALS['TSFE']->settingLanguage();
        $GLOBALS['TSFE']->settingLocale();

        return $GLOBALS['TSFE'];
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
            \TYPO3\CMS\Core\Core\Bootstrap::initializeBackendUser();
        }

        // Check for valid user
        if (is_object($GLOBALS['BE_USER']) && !empty($GLOBALS['BE_USER']->user['uid'])) {
            return true;
        }

        return false;
    }
}
