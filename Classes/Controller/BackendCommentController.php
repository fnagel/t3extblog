<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Felix Kopp <felix-source@phorax.com>
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
class Tx_T3extblog_Controller_BackendCommentController extends Tx_T3extblog_Controller_BackendBaseController {

	/**
	 * Displays all comments for a page
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('pendingComments', $this->commentRepository->findPendingByPage($this->pageId));
	}

	/**
	 * Displays all comments for a post
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post
	 *
	 * @return void
	 */
	public function listAction(Tx_T3extblog_Domain_Model_Post $post) {
		$this->view->assignMultiple(array(
			'post' => $this->postRepository->findOneByUid($post),
			'comments' => $this->commentRepository->findByPost($post, FALSE),
			'pendingComments' => $this->commentRepository->findPendingByPost($post)
		));
	}

}

?>