<?php

namespace TYPO3\T3extblog\Domain\Repository;

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

use TYPO3\T3extblog\Domain\Model\Comment;
use TYPO3\T3extblog\Domain\Model\Post;

/**
 * SubscriberRepository
 */
class SubscriberRepository extends AbstractRepository {

	/**
	 * @param Post $post The post the comment is related to
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findForNotification(Post $post) {
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
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
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
	 * @param Comment $comment
	 *
	 * @return object
	 */
	public function findForSubscriptionMail(Comment $comment) {
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
	 * @return Comment
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
