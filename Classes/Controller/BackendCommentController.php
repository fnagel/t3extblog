<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Post;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Psr\Http\Message\ResponseInterface;

/**
 * BackendCommentController.
 */
class BackendCommentController extends AbstractBackendController
{
    /**
     * Displays all comments.
     */
    public function indexAction(): ResponseInterface
    {
        $this->view->assign('comments', $this->commentRepository->findByPage($this->pageId));
        $this->view->assign('pendingComments', $this->commentRepository->findPendingByPage($this->pageId));

        return $this->htmlResponse();
    }

    /**
     * Displays all pending comments.
     */
    public function listPendingAction(): ResponseInterface
    {
        $this->view->assign('pendingComments', $this->commentRepository->findPendingByPage($this->pageId));

        return $this->htmlResponse();
    }

    
    protected function getViewHeaderButtonItems(): array
    {
        $items = parent::getViewHeaderButtonItems();
        $arguments = GeneralUtility::_GET('tx_t3extblog_web_t3extblogtxt3extblog');

        if (!empty($arguments['post'])) {
            $items['comment']['defaults'] = [
                'tx_t3blog_com' => [
                    'fk_post' => (int) $arguments['post'],
                ],
            ];
        }

        return $items;
    }

    /**
     * Displays all comments for a post.
     *
     * @param Post $post The post
     */
    public function listByPostAction(Post $post): ResponseInterface
    {
        $this->view->assignMultiple([
            'post' => $this->postRepository->findOneByUid($post),
            'comments' => $this->commentRepository->findByPost($post, false),
            'pendingComments' => $this->commentRepository->findPendingByPost($post),
        ]);

        return $this->htmlResponse();
    }
}
