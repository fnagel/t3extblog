<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Felix Nagel <info@felixnagel.com>
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
class Tx_T3extblog_Controller_CommentController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * commentRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_CommentRepository
	 */
	protected $commentRepository;

	/**
	 * injectCommentRepository
	 *
	 * @param Tx_T3extblog_Domain_Repository_CommentRepository $commentRepository
	 * @return void
	 */
	public function injectCommentRepository(Tx_T3extblog_Domain_Repository_CommentRepository $commentRepository) {
		$this->commentRepository = $commentRepository;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$comments = $this->commentRepository->findAll();
		$this->view->assign('comments', $comments);
	}

	/**
	 * action show
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return void
	 */
	public function showAction(Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->view->assign('comment', $comment);
	}

	/**
	 * action new
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment
	 * @dontvalidate $newComment
	 * @return void
	 */
	public function newAction(Tx_T3extblog_Domain_Model_Comment $newComment = NULL) {
		if ($newComment == NULL) { // workaround for fluid bug ##5636
			$newComment = t3lib_div::makeInstance('Tx_T3extblog_Domain_Model_Comment');
		}
		$this->view->assign('newComment', $newComment);
	}

	/**
	 * action create
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment
	 * @return void
	 */
	public function createAction(Tx_T3extblog_Domain_Model_Comment $newComment) {
		$this->commentRepository->add($newComment);
		$this->flashMessageContainer->add('Your new Comment was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return void
	 */
	public function editAction(Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->view->assign('comment', $comment);
	}

	/**
	 * action update
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return void
	 */
	public function updateAction(Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->commentRepository->update($comment);
		$this->flashMessageContainer->add('Your Comment was updated.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return void
	 */
	public function deleteAction(Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->commentRepository->remove($comment);
		$this->flashMessageContainer->add('Your Comment was removed.');
		$this->redirect('list');
	}

}
?>