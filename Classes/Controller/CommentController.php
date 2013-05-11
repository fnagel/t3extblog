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
class Tx_T3extblog_Controller_CommentController extends Tx_T3extblog_Controller_AbstractController {

	/**
	 * commentRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_CommentRepository
	 * @inject
	 */
	protected $commentRepository;

	/**
	 * action list
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post comments related to should be sowed
	 * @return void
	 */
	public function listAction(Tx_T3extblog_Domain_Model_Post $post) {
		$comments = $this->commentRepository->findByFkPost($post->getUid());
		
		$this->view->assign('comments', $comments);
		$this->view->assign('post', $post);
	}
	
	
	/**
	 * action new
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment
	 * @dontvalidate $newComment
	 * @return void
	 */
	public function newAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $newComment = NULL) {
		if ($newComment == NULL) { // workaround for fluid bug ##5636
			$newComment = t3lib_div::makeInstance('Tx_T3extblog_Domain_Model_Comment');
		}
		
		$this->view->assign('newComment', $newComment);
		$this->view->assign('post', $post);
	}
	
	/**
	 * Adds a comment to a blog post and redirects to single view
	 *
	 * @todo add spam check
	 * @todo add allowedUnil check
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $newComment The comment to create
	 * @return void
	 */
	public function createAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $newComment) {		
		if ($this->settings['comments']['allowed'] && $post->getAllowComments === 0) {
			// $this->checkForSpam($newComment);
			
			$newComment->setApproved($this->settings['comments']['approvedByDefault']);
			$post->addComment($newComment);
			
			$this->addFlashMessage('created');
		} else {
			$this->addFlashMessage('notAllowed');
		}	
	
		$this->redirect('show', 'Post', NULL, array('post' => $post));
	}
	
	/**
	 * action edit
	 *
	 * @todo access protection
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @return void
	 */
	public function editAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->view->assign('comment', $comment);
		$this->view->assign('post', $post);
	}

	/**
	 * action update
	 *
	 * @todo access protection
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @return void
	 */
	public function updateAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $comment) {
		$this->commentRepository->update($comment);
		$this->addFlashMessage->add('Your Comment was updated.');
		
		$this->redirect('list', NULL, NULL, array('post' => $post));
	}

	/**
	 * Deletes an existing comment
	 *
	 * @todo access protection
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 * @param Tx_T3extblog_Domain_Model_Comment $comment The comment to be deleted
	 * @return void
	 */
	public function deleteAction(Tx_T3extblog_Domain_Model_Post $post, Tx_T3extblog_Domain_Model_Comment $comment) {
		$post->removeComment($comment);
		$this->addFlashMessage('deleted', t3lib_FlashMessage::INFO);
		
		$this->redirect('show', 'Post', NULL, array('post' => $post));
	}
	
}
?>