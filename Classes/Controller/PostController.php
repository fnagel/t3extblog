<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\BackendUser;
use FelixNagel\T3extblog\Domain\Repository\CategoryRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use FelixNagel\T3extblog\Utility\GeneralUtility;
use FelixNagel\T3extblog\Domain\Model\Category;
use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Domain\Model\Comment;

/**
 * PostController.
 */
class PostController extends AbstractController
{
    /**
     * @var array
     */
    protected $cHashActions = [
        'categoryAction',
        'authorAction',
        'tagAction',
        'showAction',
    ];

    /**
     * @var PostRepository
     */
    protected $postRepository;

    /**
     * PostController constructor.
     *
     * @param PostRepository $postRepository
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * @inheritdoc
     */
    protected function handleKnownExceptionsElseThrowAgain(\Exception $exception)
    {
        if ($exception instanceof  \TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException) {
            GeneralUtility::getTsFe()->pageNotFoundAndExit('Entity not found.');
        }

        parent::handleKnownExceptionsElseThrowAgain($exception);
    }

    /**
     * Displays a list of posts.
     */
    public function listAction()
    {
        $this->view->assign('posts', $this->findPosts());
    }

    /**
     * Displays a list of posts related to a category.
     *
     * @param Category $category
     */
    public function categoryAction(Category $category)
    {
        $this->view->assign('posts', $this->findPosts($category));
    }

    /**
     * Displays a list of posts created by an author.
     *
     * @param BackendUser $author
     */
    public function authorAction(BackendUser $author)
    {
        $this->view->assign('posts', $this->findPosts($author));
    }

    /**
     * Displays a list of posts related to a tag.
     *
     * @param string $tag The name of the tag to show the posts for
     */
    public function tagAction($tag)
    {
        $posts = $this->findPosts($tag);

        if (count($posts) === 0) {
            GeneralUtility::getTsFe()->pageNotFoundAndExit('Tag not found!');
        }

        $this->view->assign('posts', $posts);
    }

    /**
     * Displays a list of latest posts.
     */
    public function latestAction()
    {
        $category = null;

        if (isset($this->settings['latestPosts']['categoryUid'])) {
            $category = $this->objectManager
                ->get(CategoryRepository::class)
                ->findByUid((int) $this->settings['latestPosts']['categoryUid']);
        }

        $this->view->assign('posts', $this->findPosts($category));
    }

    /**
     * Find all or filtered by tag, category or author.
     *
     * @param mixed $filter
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    protected function findPosts($filter = null)
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
    public function archiveAction()
    {
        $this->view->assign('posts', $this->findPosts());
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
    public function rssAction()
    {
        $this->view->assign('posts', $this->findPosts());
    }

    /**
     * Redirects permalinks to default show action.
     *
     * @param int $permalinkPost The post to display
     */
    public function permalinkAction($permalinkPost)
    {
        $post = $this->postRepository->findByUid((int) $permalinkPost);

        if ($post === null) {
            GeneralUtility::getTsFe()->pageNotFoundAndExit('Post not found!');
        }

        $this->redirect('show', 'Post', null, $post->getLinkParameter(), null, 0, 303);
    }

    /**
     * Displays one single post.
     *
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("newComment")
     *
     * @param Post    $post       The post to display
     * @param Comment $newComment A new comment
     */
    public function showAction(Post $post, Comment $newComment = null)
    {
        if ($newComment === null) {
            $newComment = $this->objectManager->get(Comment::class);
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
    }

    /**
     * Preview a post.
     *
     * @param int $previewPost The post to display
     *
     * @throws \Exception
     */
    public function previewAction($previewPost)
    {
        if (!GeneralUtility::isValidBackendUser()) {
            throw new \Exception('Preview not allowed.');
        }

        if (is_int($previewPost)) {
            $post = $this->postRepository->findByUid($previewPost, false);
            $this->forward('show', null, null, ['post' => $post]);
        }

        $this->errorAction();
    }
}
