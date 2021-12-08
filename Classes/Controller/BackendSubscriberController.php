<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ResponseInterface;

/**
 * BackendSubscriberController.
 */
class BackendSubscriberController extends AbstractBackendController
{
    /**
     * Show post subscribers.
     */
    public function indexPostSubscriberAction(): ResponseInterface
    {
        $this->view->assignMultiple([
            'postSubscribers' => $this->postSubscriberRepository->findByPage($this->pageId, false),
        ]);

        return $this->htmlResponse();
    }

    /**
     * Show blog subscribers.
     */
    public function indexBlogSubscriberAction(): ResponseInterface
    {
        $this->view->assignMultiple([
            'blogSubscribers' => $this->blogSubscriberRepository->findByPage($this->pageId, false),
        ]);

        return $this->htmlResponse();
    }
}
