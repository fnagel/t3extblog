<?php

namespace FelixNagel\T3extblog\EventListener\VisualEditor;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Controller\Event\ModifyNewContentElementWizardItemsEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener('ModifyNewContentElementWizardItems')]
class ModifyNewContentElementWizardItems
{
    public function __invoke(ModifyNewContentElementWizardItemsEvent $event): void
    {
        $items = $event->getWizardItems();
        $params = $event->getRequest()->getQueryParams();

        // Add tt_content fields for blog posts
        if (array_key_exists('tx_t3extblog_blogsystem', $params)) {
            foreach ($items as $key => $item) {
                $items[$key]['defaultValues']['irre_parenttable'] = 'tx_t3blog_post';
                $items[$key]['defaultValues']['irre_parentid'] = (int)current(
                    // Remove visual editor link anchor from post argument
                    explode('#', $params['tx_t3extblog_blogsystem']['post'])
                );
            }

            $event->setWizardItems($items);
        }
    }
}
