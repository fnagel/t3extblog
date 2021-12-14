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
 * BackendDashboardController.
 */
class BackendDashboardController extends AbstractBackendController
{
    /**
     * Blog dashboard.
     */
    public function indexAction(): ResponseInterface
    {
        $settings = $this->settings['backend']['dashboard'];

        $this->view->assignMultiple([
            'postDrafts' => $this->postRepository->findDrafts(
                $this->pageId,
                (int)$settings['postDrafts']['paginate']['itemsPerPage']
            ),
            'comments' => $this->commentRepository->findByPage(
                $this->pageId,
                (int)$settings['comments']['paginate']['itemsPerPage']
            ),
            'postSubscribers' => $this->postSubscriberRepository->findByPage(
                $this->pageId,
                false,
                (int)$settings['subscriber']['post']['paginate']['itemsPerPage']
            ),
            'blogSubscribers' => $this->blogSubscriberRepository->findByPage(
                $this->pageId,
                false,
                (int)$settings['subscriber']['blog']['paginate']['itemsPerPage']
            ),
            // For statistic
            'postCount' => $this->postRepository->findByPage($this->pageId)->count(),
            'pendingCommentsCount' => $this->commentRepository->countPendingByPage($this->pageId),
            'validCommentsCount' => $this->commentRepository->findValid($this->pageId)->count(),
            'validPostSubscribersCount' => $this->postSubscriberRepository->findByPage($this->pageId)->count(),
            'validBlogSubscribersCount' => $this->blogSubscriberRepository->findByPage($this->pageId)->count(),
        ]);

        return $this->htmlResponse();
    }
}
