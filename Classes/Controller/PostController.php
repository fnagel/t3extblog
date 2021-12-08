<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException;
use FelixNagel\T3extblog\Domain\Repository\CategoryRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use FelixNagel\T3extblog\Exception\AccessDeniedException;
use FelixNagel\T3extblog\Utility\GeneralUtility;
use FelixNagel\T3extblog\Domain\Model\Category;
use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Domain\Model\Comment;
use Psr\Http\Message\ResponseInterface;

/**
 * PostController.
 *
 * @SuppressWarnings("PHPMD.ExcessivePublicCount")
 * @SuppressWarnings("PHPMD.TooManyPublicMethods")
 */
class PostController extends AbstractCommentController
{
    protected array $cHashActions = [
        'categoryAction',
        'authorAction',
        'tagAction',
        'showAction',
    ];

    protected PostRepository $postRepository;

    /**
     * PostController constructor.
     *
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @inheritdoc
     */
    protected function handleKnownExceptionsElseThrowAgain(\Throwable $exception)
    {
        if ($exception instanceof  TargetNotFoundException) {
            $this->pageNotFoundAndExit('Entity not found.');
        }

        parent::handleKnownExceptionsElseThrowAgain($exception);
    }

    /**
     * Displays a list of posts.
     */
    public function listAction(): ResponseInterface
    {
        $this->view->assign('posts', $this->findPosts());

        return $this->htmlResponse();
    }

    /**
     * Displays a list of posts related to a category.
     */
    public function categoryAction(Category $category): ResponseInterface
    {
        $this->view->assign('posts', $this->findPosts($category));

        return $this->htmlResponse();
    }

    /**
     * Displays a list of posts created by an author.
     */
    public function authorAction(BackendUser $author): ResponseInterface
    {
        $this->view->assign('posts', $this->findPosts($author));

        return $this->htmlResponse();
    }

    /**
     * Displays a list of posts related to a tag.
     *
     * @param string $tag The name of the tag to show the posts for
     */
    public function tagAction(string $tag): ResponseInterface
    {
        $posts = $this->findPosts($tag);

        if (count($posts) === 0) {
            $this->pageNotFoundAndExit('Tag not found!');
        }

        $this->view->assign('posts', $posts);

        return $this->htmlResponse();
    }

    /**
     * Displays a list of latest posts.
     */
    public function latestAction(): ResponseInterface
    {
        $category = null;

        if (isset($this->settings['latestPosts']['categoryUid'])) {
            $category = $this->objectManager
                ->get(CategoryRepository::class)
                ->findByUid((int) $this->settings['latestPosts']['categoryUid']);
        }

        $this->view->assign('posts', $this->findPosts($category));

        return $this->htmlResponse();
    }

    /**
     * Find all or filtered by tag, category or author.
     *
     *
     */
    protected function findPosts($filter = null): QueryResultInterface
    {
        if ($filter instanceof BackendUser) {
            $this->view->assign('author', $filter);
        }

        if ($filter instanceof Category) {
            $this->view->assign('category', $filter);
        }

        if (is_string($filter) && strlen($filter) > 2) {
            $filter = urldecode($filter);
            $this->view->assign('tag', $filter);
        }

        $posts = $this->postRepository->findByFilter($filter);

        if ($posts !== null) {
            // Add basic PID based cache tag
            $this->addCacheTags($posts->getFirst());
        }

        return $posts;
    }

    /**
     * Displays archive of all posts.
     */
    public function archiveAction(): ResponseInterface
    {
        $this->view->assign('posts', $this->findPosts());

        return $this->htmlResponse();
    }

    /**
     * Initializes the current action.
     */
    public function initializeRssAction()
    {
        // set format to xml
        $this->request->setFormat('xml');
    }

    /**
     * Displays rss feed of all posts.
     */
    public function rssAction(): ResponseInterface
    {
        $this->view->assign('posts', $this->findPosts());

        return $this->htmlResponse();
    }

    /**
     * Redirects permalinks to default show action.
     *
     * @param int $permalinkPost The post to display
     */
    public function permalinkAction(int $permalinkPost)
    {
        $post = $this->postRepository->findByUid((int) $permalinkPost);

        if ($post === null) {
            $this->pageNotFoundAndExit('Post not found!');
        }

        $this->redirect('show', 'Post', null, $post->getLinkParameter(), null, 0, 303);
    }

    /**
     * Displays one single post.
     *
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("newComment")
     *
     * @param Post $post The post to display
     * @param Comment|null $newComment A new comment
     */
    public function showAction(Post $post, Comment $newComment = null): ResponseInterface
    {
        if ($newComment === null) {
            $newComment = $this->getNewComment();
        }

        // Add cache tags
        $this->addCacheTags($post);
        $this->addCacheTags('tx_t3blog_com_pid_'.$post->getPid());
        $this->addCacheTags('tx_t3blog_post_uid_'.$post->getLocalizedUid());

        // @todo: This will not work as this action is cached
        // $post->riseNumberOfViews();

        $this->view->assign('post', $post);
        $this->view->assign('newComment', $newComment);

        $this->view->assign('nextPost', $this->postRepository->nextPost($post));
        $this->view->assign('previousPost', $this->postRepository->previousPost($post));

        return $this->htmlResponse();
    }

    /**
     * Preview a post.
     *
     * @param int $previewPost The post to display
     */
    public function previewAction(int $previewPost): ResponseInterface
    {
        if (!GeneralUtility::isValidBackendUser()) {
            throw new AccessDeniedException('Preview not allowed.');
        }

        if (is_int($previewPost)) {
            $post = $this->postRepository->findByUid($previewPost, false);
            $this->forward('show', null, null, ['post' => $post]);
        }

        return $this->errorAction();
    }
}
