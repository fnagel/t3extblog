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
class Tx_T3extblog_Domain_Repository_CommentRepository extends Tx_T3extblog_Domain_Repository_AbstractRepository {

	protected $defaultOrderings = array(
		'date' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING
	);

	/**
	 * Finds all valid comments
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findValid() {
		$query = $this->createQuery();

		$query->matching(
			$this->getValidConstraints($query)
		);

		return $query->execute();
	}

	/**
	 * Finds all comments for the given post
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post
	 * @param boolean $respectEnableFields
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findByPost(Tx_T3extblog_Domain_Model_Post $post, $respectEnableFields = TRUE) {
		$query = $this->createQuery();

		$constraints = array();
		$constraints[] = $query->equals('postId', $post->getUid());

		if ($respectEnableFields === FALSE) {
			if (version_compare(TYPO3_branch, '6.0', '<')) {
				$query->getQuerySettings()->setRespectEnableFields(FALSE);
			} else {
				$query->getQuerySettings()->setIgnoreEnableFields(TRUE);
			}
			$constraints[] = $query->equals('deleted', '0');
		}

		$query->matching(
			$query->logicalAnd($constraints)
		);

		return $query->execute();
	}

	/**
	 * Finds all valid comments for the given post
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findValidByPost(Tx_T3extblog_Domain_Model_Post $post) {
		$query = $this->createQuery();

		$query->matching(
			$query->logicalAnd(
				$this->getValidConstraints($query),
				$query->equals('postId', $post->getUid())
			)
		);

		return $query->execute();
	}

	/**
	 * Finds comments by email and post uid
	 *
	 * @param string $email
	 * @param integer $postUid
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findByEmailAndPostId($email, $postUid) {
		$query = $this->createQuery();

		$query->matching(
			$this->getFindByEmailAndPostIdConstraints($query, $email, $postUid)
		);

		return $query->execute();
	}

	/**
	 * Finds valid comments by email and post uid
	 *
	 * @param string $email
	 * @param integer $postUid
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findValidByEmailAndPostId($email, $postUid) {
		$query = $this->createQuery();

		$query->matching(
			$query->logicalAnd(
				$this->getFindByEmailAndPostIdConstraints($query, $email, $postUid),
				$this->getValidConstraints($query)
			)
		);

		return $query->execute();
	}

	/**
	 * Finds pending comments by email and post uid
	 *
	 * @param string $email
	 * @param integer $postUid
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findPendingByEmailAndPostId($email, $postUid) {
		$query = $this->createQuery();

		$query->matching(
			$query->logicalAnd(
				$this->getFindByEmailAndPostIdConstraints($query, $email, $postUid),
				$this->getPendingConstraints($query)
			)
		);

		return $query->execute();
	}

	/**
	 * Finds pending comments by post
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findPendingByPost($post) {
		$query = $this->createQuery();

		$query->matching(
			$query->logicalAnd(
				$query->equals('postId', $post->getUid()),
				$this->getPendingConstraints($query)
			)
		);

		return $query->execute();
	}

	/**
	 * Finds all pending comments
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findPending() {
		$query = $this->createQuery();

		$query->matching(
			$this->getPendingConstraints($query)
		);

		return $query->execute();
	}

	/**
	 * Finds all pending comments by page
	 *
	 * @param integer $pid
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findPendingByPage($pid = 0) {
		$query = $this->createQuery(intval($pid));

		$query->matching(
			$this->getPendingConstraints($query)
		);

		return $query->execute();
	}

	/**
	 * Create constraints
	 *
	 * @param Tx_Extbase_Persistence_QueryInterface $query
	 * @param string $email
	 * @param integer $postUid
	 *
	 * @return
	 */
	protected function getFindByEmailAndPostIdConstraints(Tx_Extbase_Persistence_QueryInterface $query, $email, $postUid) {
		$constraints = $query->logicalAnd(
			$query->equals('email', $email),
			$query->equals('postId', $postUid)
		);

		return $constraints;
	}

	/**
	 * Create constraints for valid comments
	 *
	 * @param Tx_Extbase_Persistence_QueryInterface $query
	 *
	 * @return
	 */
	protected function getValidConstraints(Tx_Extbase_Persistence_QueryInterface $query) {
		$constraints = $query->logicalAnd(
			$query->equals('spam', 0),
			$query->equals('approved', 1)
		);

		return $constraints;
	}

	/**
	 * Create constraints for pending comments
	 *
	 * @param Tx_Extbase_Persistence_QueryInterface $query
	 *
	 * @return
	 */
	protected function getPendingConstraints(Tx_Extbase_Persistence_QueryInterface $query) {
		$constraints = $query->logicalOr(
			$query->equals('spam', 1),
			$query->equals('approved', 0)
		);

		return $constraints;
	}
}

?>