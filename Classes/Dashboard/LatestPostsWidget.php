<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class LatestPostsWidget extends AbstractPostWidget
{
    protected $title = self::LOCALLANG_FILE . 'widget.latestPosts.title';
    protected $description = self::LOCALLANG_FILE . 'widget.latestPosts.description';
    protected $moreItemsText = self::LOCALLANG_FILE . 'widget.latestPosts.moreItems';

    /**
     * @inheritDoc
     */
    protected function initialize()
    {
        parent::initialize();

        $this->setMoreItemsLink([
            'controller' => 'BackendPost',
            'action' => 'index',
        ]);
    }

    /**
     * @return array
     */
    protected function getListItems()
    {
        return $this->postRepository->findByPage($this->getStoragePids());
    }
}
