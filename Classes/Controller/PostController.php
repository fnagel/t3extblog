<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2014 Felix Nagel <info@felixnagel.com>
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

/**
 *
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_T3extblog_Controller_PostController extends Tx_T3extblog_Controller_AbstractController {

	/**
	 * postRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_PostRepository
	 * @inject
	 */
	protected $postRepository;

	/**
	 * Displays a list of posts.
	 *
	 * @param string                             $tag The name of the tag to show the posts for
	 * @param Tx_T3extblog_Domain_Model_Category $category
	 *
	 * @return void
	 */
	public function listAction($tag = NULL, Tx_T3extblog_Domain_Model_Category $category = NULL) {
		$this->view->assign('posts', $this->findByTagOrCategory($tag, $category));
	}

	/**
	 * Displays a list of latest posts.
	 *
	 * @param string                             $tag The name of the tag to show the posts for
	 * @param Tx_T3extblog_Domain_Model_Category $category
	 *
	 * @return void
	 */
	public function latestAction($tag = NULL, Tx_T3extblog_Domain_Model_Category $category = NULL) {
		if ($category === NULL && isset($this->settings['latestPosts']['categoryUid'])) {
			$category = t3lib_div::makeInstance("Tx_T3extblog_Domain_Repository_CategoryRepository")->findByUid((int)$this->settings['latestPosts']['categoryUid']);
		}

		$this->view->assign('posts', $this->findByTagOrCategory($tag, $category));
	}

	/**
	 * Find all or filtered by tag or by category
	 *
	 * @todo Performance improvements: do not fetch all by default, consider paginator
	 *
	 * @param string                                     $tag The name of the tag to show the posts for
	 * @param integer|Tx_T3extblog_Domain_Model_Category $category
	 *
	 * @return Tx_T3extblog_Domain_Model_Post
	 */
	private function findByTagOrCategory($tag = NULL, $category = NULL) {
		if ($category !== NULL) {
			$posts = $this->postRepository->findByCategory($category);
			$this->view->assign('category', $category);
		} elseif (strlen($tag) > 2) {
			$tag = urldecode($tag);
			$posts = $this->postRepository->findByTag($tag);
			$this->view->assign('tag', $tag);
		} else {
			$posts = $this->postRepository->findAll();
		}

		return $posts;
	}


	/**
	 * Displays archive of all posts.
	 *
	 * @return void
	 */
	public function archiveAction() {
		$posts = $this->postRepository->findAll();

		$this->view->assign('posts', $posts);
	}

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeRssAction() {
		// set format to xml
		$this->request->setFormat("xml");
	}

	/**
	 * Displays rss feed of all posts.
	 *
	 * @return void
	 */
	public function rssAction() {
		$posts = $this->postRepository->findAll();
		$this->view->assign('posts', $posts);
	}

	/**
	 * Redirects permalinks to default show action
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $permalinkPost The post to display
	 *
	 * @return void
	 */
	public function permalinkAction($permalinkPost) {
		$this->redirect('show', 'Post', NULL, $permalinkPost->getLinkParameter(), NULL, 0, 303);
	}

	/**
	 * Displays one single post
	 *
	 * @ignorevalidation $newComment
	 * @dontvalidate $newComment
	 *
	 * @param Tx_T3extblog_Domain_Model_Post    $post The post to display
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment A new comment
	 *
	 * @return void
	 */
	public function showAction(Tx_T3extblog_Domain_Model_Post $post = NULL, Tx_T3extblog_Domain_Model_Comment $newComment = NULL) {
		if ($post === NULL) {
			$this->forward('list');
		}

		if ($newComment !== NULL) {
			$this->forward('create', 'Comment');
		} else {
			$newComment = t3lib_div::makeInstance('Tx_T3extblog_Domain_Model_Comment');
		}

		// @todo: This will not work as this action is cached
        // $post->riseNumberOfViews();

		$this->view->assign('post', $post);
		$this->view->assign('newComment', $newComment);
	}

}

?>