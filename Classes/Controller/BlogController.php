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
class Tx_T3extblog_Controller_BlogController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * blogRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_BlogRepository
	 */
	protected $blogRepository;

	/**
	 * injectBlogRepository
	 *
	 * @param Tx_T3extblog_Domain_Repository_BlogRepository $blogRepository
	 * @return void
	 */
	public function injectBlogRepository(Tx_T3extblog_Domain_Repository_BlogRepository $blogRepository) {
		$this->blogRepository = $blogRepository;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$blogs = $this->blogRepository->findAll();
		$this->view->assign('blogs', $blogs);
	}

	/**
	 * action show
	 *
	 * @param Tx_T3extblog_Domain_Model_Blog $blog
	 * @return void
	 */
	public function showAction(Tx_T3extblog_Domain_Model_Blog $blog) {
		$this->view->assign('blog', $blog);
	}

}
?>