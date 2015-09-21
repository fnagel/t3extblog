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

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\T3extblog\Domain\Model\Category;
use TYPO3\T3extblog\Domain\Model\Post;

/**
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class PostRepository extends AbstractRepository {

	protected $defaultOrderings = array(
		'publishDate' => QueryInterface::ORDER_DESCENDING
	);

	/**
	 * Override default findByUid function to enable also the option to turn of
	 * the enableField setting
	 *
	 * @param integer $uid id of record
	 * @param boolean $respectEnableFields if set to false, hidden records are shown
	 *
	 * @return Post
	 */
	public function findByUid($uid, $respectEnableFields = TRUE) {
		if (version_compare(TYPO3_branch, '7.0', '<')) {
			if ($this->identityMap->hasIdentifier($uid, $this->objectType)) {
				return $this->identityMap->getObjectByIdentifier($uid, $this->objectType);
			}
		}

		$query = $this->createQuery();

		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectSysLanguage(FALSE);
		$query->getQuerySettings()->setIgnoreEnableFields(!$respectEnableFields);

		$query->matching(
			$query->logicalAnd(
				$query->equals('uid', $uid),
				$query->equals('deleted', 0)
			)
		);

		return $query->execute()->getFirst();
	}

	/**
	 * Gets post by uid
	 *
	 * Workaround as long as setRespectStoragePage does not work
	 * See related bug: https://forge.typo3.org/issues/47192
	 *
	 * @todo This should be changed to a default findByUid when above bug is fixed
	 *
	 * @param integer $uid id of record
	 * @param boolean $respectEnableFields if set to false, hidden records are shown
	 *
	 * @return Post
	 */
	public function findByLocalizedUid($uid, $respectEnableFields = TRUE) {
		$temp = $GLOBALS['TCA']['tx_t3blog_post']['ctrl']['languageField'];
		$GLOBALS['TCA']['tx_t3blog_post']['ctrl']['languageField'] = NULL;

		$post = $this->findByUid($uid, $respectEnableFields);

		$GLOBALS['TCA']['tx_t3blog_post']['ctrl']['languageField'] = $temp;

		return $post;
	}

	/**
	 * Get next post
	 *
	 * @param Post $post
	 *
	 * @return Post
	 */
	public function nextPost(Post $post) {
		$query = $this->createQuery();

		$query->setOrderings(
			array('publishDate' => QueryInterface::ORDER_ASCENDING)
		);

		$query->matching($query->greaterThan('publishDate', $post->getPublishDate()));

		return $query->execute()->getFirst();
	}

	/**
	 * Get previous post
	 *
	 * @param Post $post
	 *
	 * @return Post
	 */
	public function previousPost(Post $post) {
		$query = $this->createQuery();

		$query->matching($query->lessThan('publishDate', $post->getPublishDate()));

		return $query->execute()->getFirst();
	}

	/**
	 * Returns all objects with specific PID
	 *
	 * @param integer $pid
	 * @param boolean $respectEnableFields
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByPage($pid = 0, $respectEnableFields = TRUE) {
		$query = $this->createQuery((int) $pid);

		if ($respectEnableFields === FALSE) {
			$query->getQuerySettings()->setIgnoreEnableFields(TRUE);

			$query->matching(
				$query->equals('deleted', '0')
			);
		}

		return $query->execute();
	}


	/**
	 * Finds posts by the specified tag
	 *
	 * @param string $tag
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByTag($tag) {
		$query = $this->createQuery();

		$query->matching(
			$query->like('tagCloud', '%' . $tag . '%')
		);

		return $query->execute();
	}

	/**
	 * Returns all objects of this repository with matching category
	 *
	 * @param Category $category
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findByCategory($category) {
		$query = $this->createQuery();

		$constraints = array();
		$constraints[] = $query->contains('categories', $category);

		$categories = $category->getChildCategories();

		if (count($categories) > 0) {
			foreach ($categories as $childCategory) {
				$constraints[] = $query->contains('categories', $childCategory);
			}
		}

		$query->matching($query->logicalOr($constraints));

		return $query->execute();
	}

}
