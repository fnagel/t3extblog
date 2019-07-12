<?php

namespace FelixNagel\T3extblog\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2018 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\GeneralUtility as CoreGeneralUtility;
use TYPO3\CMS\Frontend\Utility\EidUtility;

/**
 * General utility class.
 *
 * @todo Rename to FrontendUtility
 */
class GeneralUtility
{
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
            return self::generateTypoScriptFrontendController($pageUid);
        }

        return $GLOBALS['TSFE'];
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
        EidUtility::initTCA();

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
     * Check if a valid BE login exists.
     *
     * $GLOBALS['TSFE']->isBackendUserLoggedIn() (and TS equivalent) does not work.
     * See https://forge.typo3.org/issues/23625
     *
     * @todo Workaround for bug in TYPO3
     *
     * @return bool
     */
    public static function isValidBackendUser()
    {
        // Init if needed
        if (!isset($GLOBALS['BE_USER'])) {
            $bootstrap = \TYPO3\CMS\Core\Core\Bootstrap::getInstance();
            $bootstrap->initializeBackendUser();
        }

        // Check for valid user
        if (is_object($GLOBALS['BE_USER']) && !empty($GLOBALS['BE_USER']->user['uid'])) {
            return true;
        }

        return false;
    }
}
