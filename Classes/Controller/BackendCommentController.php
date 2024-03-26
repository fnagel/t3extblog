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
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * BackendCommentController.
 */
class BackendCommentController extends AbstractBackendController
{
    /**
     * Displays all comments.
     */
    public function indexAction(int $page = 1): ResponseInterface
    {
        $this->view->assign('pendingCommentsCount', $this->commentRepository->countPendingByPage($this->pageId));

        return $this->response(
            $this->commentRepository->findByPage($this->pageId),
            $page
        );
    }

    /**
     * Displays all pending comments.
     */
    public function listPendingAction(int $page = 1): ResponseInterface
    {
        return $this->response(
            $this->commentRepository->findPendingByPage($this->pageId),
            $page
        );
    }

    /**
     * Displays all comments for a post.
     */
    public function listByPostAction(Post $post, int $page = 1): ResponseInterface
    {
        $this->view->assignMultiple([
            'post' => $this->postRepository->findOneByUid($post),
            'pendingCommentsCount' => $this->commentRepository->countPendingByPost($post),
        ]);

        return $this->response(
            $this->commentRepository->findByPost($post, false),
            $page
        );
    }

    protected function response(QueryResultInterface $result, int $page = 1): ResponseInterface
    {
        return $this->paginationHtmlResponse(
            $result,
            $this->settings['backend']['comments']['paginate'],
            $page
        );
    }

    protected function getViewHeaderButtonItems(): array
    {
        $items = parent::getViewHeaderButtonItems();
        $arguments = $this->request->getQueryParams()['tx_t3extblog_web_t3extblogtxt3extblog'] ?? null;

        if (!empty($arguments['post'])) {
            $items['comment']['defaults'] = [
                'tx_t3blog_com' => [
                    'fk_post' => (int) $arguments['post'],
                ],
            ];
        }

        return $items;
    }
}
