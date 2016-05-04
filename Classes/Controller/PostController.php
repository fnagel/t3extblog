<?php

namespace TYPO3\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2016 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\T3extblog\Domain\Model\BackendUser;
use TYPO3\T3extblog\Utility\GeneralUtility;
use TYPO3\T3extblog\Domain\Model\Category;
use TYPO3\T3extblog\Domain\Model\Post;
use TYPO3\T3extblog\Domain\Model\Comment;

/**
 * PostController
 */
class PostController extends AbstractController {

	/**
	 * postRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\PostRepository
	 * @inject
	 */
	protected $postRepository;

	/**
	 * Displays a list of posts.
	 *
	 * @return void
	 */
	public function listAction() {
		$this->view->assign('posts', $this->findPosts());
	}

	/**
	 * Displays a list of posts related to a category
	 *
	 * @param Category $category
	 *
	 * @return void
	 */
	public function categoryAction(Category $category) {
		$this->view->assign('posts', $this->findPosts($category));
	}

	/**
	 * Displays a list of posts created by an author
	 *
	 * @param BackendUser $author
	 *
	 * @return void
	 */
	public function authorAction(BackendUser $author) {
		$this->view->assign('posts', $this->findPosts($author));
	}

	/**
	 * Displays a list of posts related to a tag
	 *
	 * @param string $tag The name of the tag to show the posts for
	 *
	 * @return void
	 */
	public function tagAction($tag) {
		$posts = $this->findPosts($tag);

		if (count($posts) === 0) {
			GeneralUtility::getTsFe()->pageNotFoundAndExit('Tag not found!');
		}

		$this->view->assign('posts', $posts);
	}

	/**
	 * Displays a list of latest posts.
	 *
	 * @return void
	 */
	public function latestAction() {
		$category = NULL;

		if (isset($this->settings['latestPosts']['categoryUid'])) {
			$category = $this->objectManager
				->get('TYPO3\\T3extblog\\Domain\\Repository\\CategoryRepository')
				->findByUid((int) $this->settings['latestPosts']['categoryUid']);
		}

		$this->view->assign('posts', $this->findPosts($category));
	}

	/**
	 * Find all or filtered by tag, category or author
	 *
	 * @param mixed $filter
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	protected function findPosts($filter = NULL) {
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

		if ($posts !== NULL) {
			// Add basic PID based cache tag
			$this->addCacheTags($posts->getFirst());
		}

		return $posts;
	}

	/**
	 * Displays archive of all posts.
	 *
	 * @return void
	 */
	public function archiveAction() {
		$this->view->assign('posts', $this->findPosts());
	}

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeRssAction() {
		// set format to xml
		$this->request->setFormat('xml');
	}

	/**
	 * Displays rss feed of all posts.
	 *
	 * @return void
	 */
	public function rssAction() {
		$this->view->assign('posts', $this->findPosts());
	}

	/**
	 * Redirects permalinks to default show action
	 *
	 * @param int $permalinkPost The post to display
	 *
	 * @return void
	 */
	public function permalinkAction($permalinkPost) {
		$post = $this->postRepository->findByUid((int) $permalinkPost);

		if ($post === NULL) {
			GeneralUtility::getTsFe()->pageNotFoundAndExit('Post not found!');
		}

		$this->redirect('show', 'Post', NULL, $post->getLinkParameter(), NULL, 0, 303);
	}

	/**
	 * Displays one single post
	 *
	 * @ignorevalidation $newComment
	 * @dontvalidate $newComment
	 *
	 * @param Post $post The post to display
	 * @param Comment $newComment A new comment
	 *
	 * @return void
	 */
	public function showAction(Post $post, Comment $newComment = NULL) {
		if ($newComment === NULL) {
			$newComment = $this->objectManager->get('TYPO3\\T3extblog\\Domain\\Model\\Comment');
		}

		// Add cache tags
		$this->addCacheTags($post);
		$this->addCacheTags('tx_t3blog_com_pid_' . $post->getPid());
		$this->addCacheTags('tx_t3blog_post_uid_' . $post->getLocalizedUid());

		// @todo: This will not work as this action is cached
		// $post->riseNumberOfViews();

		$this->view->assign('post', $post);
		$this->view->assign('newComment', $newComment);

		$this->view->assign('nextPost', $this->postRepository->nextPost($post));
		$this->view->assign('previousPost', $this->postRepository->previousPost($post));
	}

	/**
	 * Preview a post
	 * 
	 * Testing does not work on local environment
	 * Issues when baseUrl AND absRefPrefix are set
	 *
	 * @param integer $previewPost The post to display
	 *
	 * @throws \Exception
	 *
	 * @return void
	 */
	public function previewAction($previewPost) {
		if (!GeneralUtility::isValidBackendUser()) {
			throw new \Exception('Preview not allowed.');
		}

		if (is_int($previewPost)) {
			$post = $this->postRepository->findByUid($previewPost, FALSE);
			$this->forward('show', NULL, NULL, array('post' => $post));
		}

		$this->errorAction();
	}

}
