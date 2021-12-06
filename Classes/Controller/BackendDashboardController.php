<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * BackendDashboardController.
 */
class BackendDashboardController extends AbstractBackendController
{
    /**
     * Blog dashboard.
     */
    public function indexAction()
    {
        $this->view->assignMultiple([
            'postDrafts' => $this->postRepository->findDrafts($this->pageId),
            'comments' => $this->commentRepository->findByPage($this->pageId),
            'pendingComments' => $this->commentRepository->findPendingByPage($this->pageId),
            'postSubscribers' => $this->postSubscriberRepository->findByPage($this->pageId, false),
            'blogSubscribers' => $this->blogSubscriberRepository->findByPage($this->pageId, false),
            // For statistic
            'postCount' => $this->postRepository->findByPage($this->pageId)->count(),
            'validCommentsCount' => $this->commentRepository->findValid($this->pageId)->count(),
            'validPostSubscribersCount' => $this->postSubscriberRepository->findByPage($this->pageId)->count(),
            'validBlogSubscribersCount' => $this->blogSubscriberRepository->findByPage($this->pageId)->count(),
        ]);
    }
}
