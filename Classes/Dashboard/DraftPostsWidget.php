<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class DraftPostsWidget extends AbstractPostWidget
{
    protected $title = self::LOCALLANG_FILE . 'widget.draftPosts.title';
    protected $description = self::LOCALLANG_FILE . 'widget.draftPosts.description';

    /**
     * @inheritDoc
     */
    protected $height = 2;

    /**
     * @return array
     */
    protected function getListItems()
    {
        return $this->postRepository->findDrafts($this->getStoragePids());
    }
}
