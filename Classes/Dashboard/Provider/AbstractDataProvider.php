<?php

namespace FelixNagel\T3extblog\Dashboard\Provider;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Localization\LanguageService;

abstract class AbstractDataProvider
{
    use ProviderTrait;

    /**
     * @var string
     */
    const LOCALLANG_FILE = 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:';

    /**
     * @return int
     */
    protected function getStoragePids()
    {
        $pages = $this->getBlogPageUids();

        // @todo Remove this and make repo method able to use multiple PIDs
        return (count($pages) === 1) ? current($pages) : -1;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function translate($key)
    {
        return $this->getLanguageService()->sL(self::LOCALLANG_FILE . $key);
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
