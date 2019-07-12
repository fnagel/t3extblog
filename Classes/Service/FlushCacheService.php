<?php

namespace FelixNagel\T3extblog\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016-2018 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Handles email sending and templating.
 */
class FlushCacheService implements SingletonInterface
{
    /**
     * @var array
     */
    protected $cacheTagsToFlush = [];

    /**
     */
    public function initializeObject()
    {
        // Clear cache on shutdown
        register_shutdown_function([$this, 'flushFrontendCache']);
    }

    /**
     * Clear all added cache tags. Called on shutdown.
     */
    public function flushFrontendCache()
    {
        $this->flushFrontendCacheByTags($this->cacheTagsToFlush);
    }

    /**
     * Add a cache tag to flush.
     *
     * @param string|array $cacheTagsToFlush
     *
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public function addCacheTagsToFlush($cacheTagsToFlush)
    {
        if (!is_array($cacheTagsToFlush)) {
            $cacheTagsToFlush = [$cacheTagsToFlush];
        }

        if (count($cacheTagsToFlush) < 1) {
            return;
        }

        $this->cacheTagsToFlush = array_merge($this->cacheTagsToFlush, $cacheTagsToFlush);
    }

    /**
     * Clear frontend page cache by tags.
     *
     * @param $cacheTagsToFlush
     *
     * @throws \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException
     */
    public static function flushFrontendCacheByTags($cacheTagsToFlush)
    {
        if (count($cacheTagsToFlush) < 1) {
            return;
        }

        /** @var $cacheManager CacheManager */
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);

        foreach (array_unique($cacheTagsToFlush) as $cacheTag) {
            $cacheManager->getCache('cache_pages')->flushByTag($cacheTag);
            $cacheManager->getCache('cache_pagesection')->flushByTag($cacheTag);
        }
    }
}
