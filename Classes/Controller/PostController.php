<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use FelixNagel\T3extblog\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException;
use FelixNagel\T3extblog\Domain\Repository\CategoryRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use FelixNagel\T3extblog\Exception\AccessDeniedException;
use FelixNagel\T3extblog\Utility\FrontendUtility;
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
    /**
     * PostController constructor.
     *
     */
    public function __construct(protected PostRepository $postRepository)
    {
    }

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
    public function listAction(int $page = 1): ResponseInterface
    {
        return $this->paginationHtmlResponse(
            $this->findPosts(),
            $this->settings['blogsystem']['posts']['paginate'],
            $page
        );
    }

    /**
     * Displays a list of posts related to a category.
     */
    public function categoryAction(Category $category, int $page = 1): ResponseInterface
    {
        return $this->paginationHtmlResponse(
            $this->findPosts($category),
            $this->settings['blogsystem']['posts']['paginate'],
            $page
        );
    }

    /**
     * Displays a list of posts created by an author.
     */
    public function authorAction(BackendUser $author, int $page = 1): ResponseInterface
    {
        return $this->paginationHtmlResponse(
            $this->findPosts($author),
            $this->settings['blogsystem']['posts']['paginate'],
            $page
        );
    }

    /**
     * Displays a list of posts related to a tag.
     *
     * @param string $tag The name of the tag to show the posts for
     */
    public function tagAction(string $tag, int $page = 1): ResponseInterface
    {
        $posts = $this->findPosts($tag);

        if (count($posts) === 0) {
            $this->pageNotFoundAndExit('Tag not found!');
        }

        return $this->paginationHtmlResponse(
            $posts,
            $this->settings['blogsystem']['posts']['paginate'],
            $page
        );
    }

    /**
     * Displays a list of latest posts.
     */
    public function latestAction(int $page = 1): ResponseInterface
    {
        $category = null;

        if (isset($this->settings['latestPosts']['categoryUid'])) {
            $category = GeneralUtility::makeInstance(CategoryRepository::class)
                ->findByUid((int) $this->settings['latestPosts']['categoryUid']);
        }

        return $this->paginationHtmlResponse(
            $this->findPosts($category),
            $this->settings['latestPosts']['paginate'],
            $page
        );
    }

    /**
     * Find all or filtered by tag, category or author.
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
     * Set Format to xml for the RSS action.
     */
    public function initializeRssAction(): void
    {
        $this->request->setFormat('xml');
    }

    /**
     * Displays rss feed of all posts.
     */
    public function rssAction(): ResponseInterface
    {
        return $this->paginationHtmlResponse(
            $this->findPosts(),
            $this->settings['rss']['paginate']
        );
    }

    /**
     * Redirects permalinks to default show action.
     */
    public function permalinkAction(int $permalinkPost): ResponseInterface
    {
        $post = $this->postRepository->findByUid($permalinkPost);

        if ($post === null) {
            $this->pageNotFoundAndExit('Post not found!');
        }

        return $this->redirect('show', 'Post', null, $post->getLinkParameter());
    }

    /**
     * Displays one single post.
     */
    #[IgnoreValidation(['value' => 'newComment'])]
    public function showAction(Post $post, int $page = 1, Comment $newComment = null): ResponseInterface
    {
        if (!$newComment instanceof Comment) {
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

        return $this->paginationHtmlResponse(
            $post->getCommentsForPaginate(),
            $this->settings['blogsystem']['comments']['paginate'],
            $page
        );
    }

    /**
     * Preview a post.
     */
    public function previewAction($previewPost): ResponseInterface
    {
        if (!FrontendUtility::isValidBackendUser()) {
            throw new AccessDeniedException('Preview not allowed.');
        }

        if (is_int($previewPost)) {
            $post = $this->postRepository->findByUid($previewPost, false);
            return (new ForwardResponse('show'))->withArguments(['post' => $post]);
        }

        return $this->errorAction();
    }
}
