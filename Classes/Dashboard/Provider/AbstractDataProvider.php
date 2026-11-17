<?php

namespace FelixNagel\T3extblog\Dashboard\Provider;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Exception\AccessDeniedException;
use FelixNagel\T3extblog\Utility\PermissionUtility;
use TYPO3\CMS\Core\Localization\LanguageService;

abstract class AbstractDataProvider
{
    use ProviderTrait;

    /**
     * @var string
     */
    public const LOCALLANG_FILE = 't3extblog.dashboard:';

    /**
     * Returns the blog pages the current backend user has access to.
     *
     * @return int[]
     */
    protected function getStoragePids(string $table): array
    {
        PermissionUtility::assertTableAccess([$table]);

        $pages = $this->getBlogPageUids();

        if ($pages === []) {
            throw new AccessDeniedException(
                'No accessible blog page found. ' . PermissionUtility::ACCESS_DENIED_HINT,
                1794816005
            );
        }

        return $pages;
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
