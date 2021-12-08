<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
        static::flushFrontendCacheByTags($this->cacheTagsToFlush);
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
