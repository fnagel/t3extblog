<?php

namespace FelixNagel\T3extblog\Utility;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\LocalizedEntityInterface;
use TYPO3\CMS\Core\Localization\Locale;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SiteUtility implements SingletonInterface
{
    public static function getConfiguration(int $pid): array
    {
        return static::getSite($pid)->getConfiguration();
    }

    public static function getLanguage(int $pid, int $languageUid = 0): SiteLanguage
    {
        return static::getSite($pid)->getLanguageById($languageUid);
    }

    public static function getLocale(LocalizedEntityInterface $entity): Locale
    {
        return static::getSite($entity->getPid())->getLanguageById($entity->getSysLanguageUid())->getLocale();
    }

    protected static function getSite(int $pid): Site
    {
        return GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($pid);
    }
}
