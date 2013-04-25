<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 
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
class Tx_T3extblog_Controller_PostsController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * postsRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_PostsRepository
	 */
	protected $postsRepository;

	/**
	 * injectPostsRepository
	 *
	 * @param Tx_T3extblog_Domain_Repository_PostsRepository $postsRepository
	 * @return void
	 */
	public function injectPostsRepository(Tx_T3extblog_Domain_Repository_PostsRepository $postsRepository) {
		$this->postsRepository = $postsRepository;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$postss = $this->postsRepository->findAll();
		$this->view->assign('postss', $postss);
	}

	/**
	 * action show
	 *
	 * @param Tx_T3extblog_Domain_Model_Posts $posts
	 * @return void
	 */
	public function showAction(Tx_T3extblog_Domain_Model_Posts $posts) {
		$this->view->assign('posts', $posts);
	}

}
?>