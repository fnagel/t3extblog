<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\CommentRepository;

abstract class AbstractCommentsWidget extends AbstractListWidget
{
    /**
     * @inheritDoc
     */
    protected $templateName = 'CommentWidget';

    /**
     * @inheritDoc
     */
    protected $width = 3;

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

        $this->items = $this->getListItems();
    }
}
