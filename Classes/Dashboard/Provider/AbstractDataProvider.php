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
    public const LOCALLANG_FILE = 'LLL:EXT:t3extblog/Resources/Private/Language/locallang_dashboard.xlf:';

    
    protected function getStoragePids(): int
    {
        $pages = $this->getBlogPageUids();

        // @todo Remove this and make repo method able to use multiple PIDs
        return (count($pages) === 1) ? current($pages) : -1;
    }

    
    protected function translate(string $key): string
    {
        return $this->getLanguageService()->sL(self::LOCALLANG_FILE . $key);
    }

    
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
