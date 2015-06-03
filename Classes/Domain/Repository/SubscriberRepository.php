<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
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
class Tx_T3extblog_Domain_Repository_SubscriberRepository extends Tx_T3extblog_Domain_Repository_AbstractRepository {

	/**
	 *
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post The post the comment is related to
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findForNotification(Tx_T3extblog_Domain_Model_Post $post) {
		$query = $this->createQuery();

		$query->matching(
			$query->equals('postUid', $post->getUid())
		);

		return $query->execute();
	}

	/**
	 * Searchs for already registered subscriptions
	 *
	 * @param integer $postUid
	 * @param string $email
	 * @param integer $excludeUid
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findExistingSubscriptions($postUid, $email, $excludeUid = NULL) {
		$query = $this->createQuery();
		$constraints = array();

		$constraints[] = $query->equals('postUid', $postUid);
		$constraints[] = $query->equals('email', $email);

		if ($excludeUid !== NULL) {
			$constraints[] = $query->logicalNot($query->equals('uid', intval($excludeUid)));
		}

		$query->matching(
			$query->logicalAnd($constraints)
		);

		return $query->execute();
	}

	/**
	 * Finds subscriber without opt-in mail sent before
	 *
	 * @param Tx_T3extblog_Domain_Model_Comment $comment
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface The comments
	 */
	public function findForSubscriptionMail(Tx_T3extblog_Domain_Model_Comment $comment) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE);

		$query->matching(
			$query->logicalAnd(
				$query->equals('postUid', $comment->getPostId()),
				$query->equals('email', $comment->getEmail()),
				$query->equals('lastSent', 0),
				$query->equals('hidden', 1),
				$query->equals('deleted', 0)
			)
		);

		return $query->execute()->getFirst();
	}

	/**
	 * Find by code
	 *
	 * @param string $code
	 * @param boolean $enableFields
	 *
	 * @return Tx_T3extblog_Domain_Model_Comment
	 */
	public function findByCode($code, $enableFields = TRUE) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(!$enableFields);

		$query->matching(
			$query->logicalAnd(
				$query->equals('code', $code),
				$query->equals('deleted', 0)
			)
		);

		return $query->execute()->getFirst();
	}

}

?>