<?php

namespace FelixNagel\T3extblog\Utility;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Checks Site configuration.
 */
class SiteConfigurationValidator
{
    /**
     * Check needed site configuration values.
     */
    public static function validate(int $pageId): void
    {
        if ($pageId === 0) {
            return;
        }

        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $site = $siteFinder->getSiteByPageId($pageId);
        $configuration = $site->getConfiguration();

        if (is_array($configuration) && str_starts_with($configuration['base'], '/')) {
            throw new InvalidConfigurationException(
                'The "base" URL property in your TYPO3 site configuration must be absolute.
				Use something like "https://domain.tld" instead of "/", otherwise emails will have broken links!',
                1700092666
            );
        }
    }
}
