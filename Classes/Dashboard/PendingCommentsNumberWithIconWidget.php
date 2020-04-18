<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\CommentRepository;

class PendingCommentsNumberWithIconWidget extends AbstractNumberWithIconWidget
{
    protected $title = self::LOCALLANG_FILE . 'widget.pendingCommentsNumberWithIcon.title';
    protected $description = self::LOCALLANG_FILE . 'widget.pendingCommentsNumberWithIcon.description';
    protected $subtitle = self::LOCALLANG_FILE . 'widget.pendingCommentsNumberWithIcon.subtitle';

    /**
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     * @inheritDoc
     */
    protected function initialize()
    {
        parent::initialize();

        $this->commentRepository = $this->objectManager->get(CommentRepository::class);

        $this->link = $this->getModuleLink(null, [
            'controller' => 'BackendComment',
            'action' => 'listPending',
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function updateNumber()
    {
        $this->number = $this->commentRepository->findPendingByPage($this->getStoragePids())->count();
    }
}
