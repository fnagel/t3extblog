<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\PostRepository;

abstract class AbstractPostWidget extends AbstractListWidget
{
    /**
     * @inheritDoc
     */
    protected $templateName = 'PostWidget';

    /**
     * @inheritDoc
     */
    protected $width = 3;

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * @inheritDoc
     */
    protected function initialize()
    {
        parent::initialize();

        $this->postRepository = $this->objectManager->get(PostRepository::class);

        $this->items = $this->getListItems();
    }
}
