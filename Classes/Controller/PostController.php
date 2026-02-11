<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
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
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
class PostController extends AbstractCommentController
{
    public function __construct(protected PostRepository $postRepository)
    {
    }

    protected function handleKnownExceptionsElseThrowAgain(\Throwable $exception): never
    {
        if ($exception instanceof TargetNotFoundException) {
            // @extensionScannerIgnoreLine
            $this->pageNotFoundAndExit();
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

    public static function isPostShowPage(?ServerRequestInterface $request): int|false
    {
        /* @var $routing PageArguments */
        $routing = $request->getAttribute('routing');

        return isset(
            $routing->getArguments()['tx_t3extblog_blogsystem']['controller'],
            $routing->getArguments()['tx_t3extblog_blogsystem']['action'],
            $routing->getArguments()['tx_t3extblog_blogsystem']['post'],
        ) &&
            $routing->getArguments()['tx_t3extblog_blogsystem']['controller'] === 'Post' &&
            $routing->getArguments()['tx_t3extblog_blogsystem']['action'] === 'show' &&
            MathUtility::canBeInterpretedAsInteger($routing->getArguments()['tx_t3extblog_blogsystem']['post']) ?
            (int)$routing->getArguments()['tx_t3extblog_blogsystem']['post'] : false;
    }

    /**
     * Displays a list of related posts.
     */
    public function relatedAction(int $page = 1): ResponseInterface
    {
        if (($id = static::isPostShowPage($this->request)) === false ||
            ($post = $this->postRepository->findByUid($id)) === null
        ) {
            return $this->htmlResponse('');
        }

        $this->view->assign('post', $post);

        return $this->paginationHtmlResponse(
            $this->postRepository->relatedPosts($post),
            $this->settings['relatedPosts']['paginate'],
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
            // @extensionScannerIgnoreLine
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
     * Displays rss feed of all posts.
     */
    public function rssAction(): ResponseInterface
    {
        return $this->paginationXmlResponse($this->findPosts(), $this->settings['rss']['paginate']);
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
    public function showAction(Post $post, int $page = 1, ?Comment $newComment = null): ResponseInterface
    {
        if (!$newComment instanceof Comment) {
            $newComment = $this->getNewComment();
        }

        // Add cache tags
        // @extensionScannerIgnoreLine
        $this->addCacheTags($post);
        // @extensionScannerIgnoreLine
        $this->addCacheTags('tx_t3blog_com_pid_'.$post->getPid());
        // @extensionScannerIgnoreLine
        $this->addCacheTags('tx_t3blog_post_uid_'.$post->getLocalizedUid());

        // @todo: Implement this! See https://github.com/fnagel/t3extblog/issues/22
        // $post->riseNumberOfViews();

        $this->view->assign('post', $post);
        $this->view->assign('newComment', $newComment);

        // Related posts
        if ($this->settings['blogsystem']['posts']['nextAndPreviousPosts']['enable']) {
            $this->view->assign('nextPost', $this->postRepository->nextPost($post));
            $this->view->assign('previousPost', $this->postRepository->previousPost($post));
        }
        if ($this->settings['blogsystem']['posts']['relatedPosts']['enable']) {
            $this->view->assign('relatedPosts', $this->getPaginationVariables(
                $this->postRepository->relatedPosts($post),
                $this->settings['blogsystem']['posts']['relatedPosts']['paginate'],
            ));
        }

        return $this->paginationHtmlResponse(
            $post->getCommentsForPaginate(),
            $this->settings['blogsystem']['comments']['paginate'],
            $page
        );
    }

    /**
     * Preview a post.
     */
    public function previewAction(int $previewPost): ResponseInterface
    {
        if (!FrontendUtility::isValidBackendUser()) {
            throw new AccessDeniedException('Preview not allowed.');
        }

        $post = $this->postRepository->findByUid($previewPost, false);

        if ($post instanceof Post) {
            return (new ForwardResponse('show'))->withArguments(['post' => $post]);
        }

        return $this->errorAction();
    }
}
