<?php

namespace FelixNagel\T3extblog\Dashboard;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository;

class PostSubscriberWidget extends AbstractSubscriberWidget
{
    protected $title = self::LOCALLANG_FILE . 'widget.postSubscriber.title';
    protected $description = self::LOCALLANG_FILE . 'widget.postSubscriber.description';
    protected $moreItemsText = self::LOCALLANG_FILE . 'widget.postSubscriber.moreItems';

    /**
     * @inheritDoc
     */
    protected function initialize()
    {
        parent::initialize();

        $this->subscriberRepository = $this->objectManager->get(PostSubscriberRepository::class);

        $this->items = $this->getListItems();

        $this->setMoreItemsLink([
            'controller' => 'BackendSubscriber',
            'action' => 'indexPostSubscriber',
        ]);

        $this->view->assign('table', 'tx_t3blog_com_nl');
    }
}
