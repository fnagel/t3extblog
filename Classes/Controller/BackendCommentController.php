<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Post;

/**
 * BackendCommentController.
 */
class BackendCommentController extends BackendBaseController
{
    /**
     * Displays all comments.
     */
    public function indexAction()
    {
        $this->view->assign('comments', $this->commentRepository->findByPage($this->pageId));
        $this->view->assign('pendingComments', $this->commentRepository->findPendingByPage($this->pageId));
    }

    /**
     * Displays all pending comments.
     */
    public function listPendingAction()
    {
        $this->view->assign('pendingComments', $this->commentRepository->findPendingByPage($this->pageId));
    }

    /**
     * Displays all comments for a post.
     *
     * @param Post $post The post
     */
    public function listByPostAction(Post $post)
    {
        $this->view->assignMultiple([
            'post' => $this->postRepository->findOneByUid($post),
            'comments' => $this->commentRepository->findByPost($post, false),
            'pendingComments' => $this->commentRepository->findPendingByPost($post),
        ]);
    }
}
