<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class PendingCommentsWidget extends AbstractCommentsWidget
{
    protected $title = self::LOCALLANG_FILE . 'widget.pendingComments.title';
    protected $description = self::LOCALLANG_FILE . 'widget.pendingComments.description';
    protected $moreItemsText = self::LOCALLANG_FILE . 'widget.pendingComments.moreItems';

    /**
     * @inheritDoc
     */
    protected function initialize()
    {
        parent::initialize();

        $this->setMoreItemsLink([
            'controller' => 'BackendComment',
            'action' => 'listPending',
        ]);
    }

    /**
     * @return array
     */
    protected function getListItems()
    {
        return $this->commentRepository->findPendingByPage($this->getStoragePids());
    }
}
