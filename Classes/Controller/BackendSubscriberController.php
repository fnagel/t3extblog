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
    protected array $requiredTables = [
        'indexPostSubscriber' => ['tx_t3blog_com_nl'],
        'indexBlogSubscriber' => ['tx_t3blog_blog_nl'],
    ];

    /**
     * Show post subscribers.
     */
    public function indexPostSubscriberAction(int $page = 1): ResponseInterface
    {
        return $this->paginationHtmlResponse(
            'Subscriber/IndexPostSubscriber',
            $this->postSubscriberRepository->findByPage($this->pageId, false),
            $this->settings['backend']['subscriber']['post']['paginate'],
            $page
        );
    }

    /**
     * Show blog subscribers.
     */
    public function indexBlogSubscriberAction(int $page = 1): ResponseInterface
    {
        return $this->paginationHtmlResponse(
            'Subscriber/IndexBlogSubscriber',
            $this->blogSubscriberRepository->findByPage($this->pageId, false),
            $this->settings['backend']['subscriber']['blog']['paginate'],
            $page
        );
    }
}
