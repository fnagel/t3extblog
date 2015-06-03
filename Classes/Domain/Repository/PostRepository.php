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
class Tx_T3extblog_Domain_Repository_PostRepository extends Tx_T3extblog_Domain_Repository_AbstractRepository {

	protected $defaultOrderings = array(
		'publishDate' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING
	);

	/**
	 * Override default findByUid function to enable also the option to turn of
	 * the enableField setting
	 *
	 * @param integer $uid id of record
	 * @param boolean $respectEnableFields if set to false, hidden records are shown
	 *
	 * @return Tx_T3extblog_Domain_Model_Post
	 */
	public function findByUid($uid, $respectEnableFields = TRUE) {
		if ($this->identityMap->hasIdentifier($uid, $this->objectType)) {
			return $this->identityMap->getObjectByIdentifier($uid, $this->objectType);
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
	 * Gets a minimal model of a localized blog post
	 *
	 * Workaround as long as setRespectStoragePage does not work
	 * See related bug: https://forge.typo3.org/issues/47192
	 *
	 * @todo This should be changed to a default findByUid when above bug is fixed
	 *
	 * @param integer $uid id of record
	 *
	 * @return Tx_T3extblog_Domain_Model_Post
	 */
	public function findByLocalizedUidForSubscriptionList($uid) {
		/* @var $database t3lib_DB */
		$database = $GLOBALS['TYPO3_DB'];
		$table = 'tx_t3blog_post';
		$select = 'uid, pid, sys_language_uid as _languageUid, l18n_parent as _localizedUid, deleted, hidden, title, author, date as publishDate';
		$where = 'uid = ' . $uid . $this->enableFields($table);
		$result = $database->exec_SELECTgetSingleRow($select, $table, $where);

		/* @var $post Tx_T3extblog_Domain_Model_Post */
		$post = $this->objectManager->create('Tx_T3extblog_Domain_Model_Post');

		foreach ($result as $key => $value) {
			// Prepare DateTime model properties
			if ($key === 'publishDate' && !empty($value)) {
				$tempDate = new \DateTime();
				$tempDate->setTimestamp((int) $value);
				$value = $tempDate;
			}

			$post->_setProperty($key, $value);
		}

		return $post;
	}

	/**
	 * Get next post
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post
	 *
	 * @return Tx_T3extblog_Domain_Model_Post
	 */
	public function nextPost(Tx_T3extblog_Domain_Model_Post $post) {
		$query = $this->createQuery();

		$query->setOrderings(
			array('publishDate' => Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING)
		);

		$query->matching($query->greaterThan('publishDate', $post->getPublishDate()));

		return $query->execute()->getFirst();
	}

	/**
	 * Get previous post
	 *
	 * @param Tx_T3extblog_Domain_Model_Post $post
	 *
	 * @return Tx_T3extblog_Domain_Model_Post
	 */
	public function previousPost(Tx_T3extblog_Domain_Model_Post $post) {
		$query = $this->createQuery();

		$query->matching($query->lessThan('publishDate', $post->getPublishDate()));

		return $query->execute()->getFirst();
	}

	/**
	 * Returns all objects of this repository
	 *
	 * @param integer $pid
	 * @param boolean $respectEnableFields
	 *
	 * @return Tx_Extbase_Persistence_QueryResultInterface  The posts
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
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
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
	 * @param Tx_T3extblog_Domain_Model_Category $category
	 *
	 * @return Tx_Extbase_Persistence_QueryResult
	 */
	public function findByCategory($category) {
		$query = $this->createQuery();

		$constraints = array();
		$constraints[] = $query->contains('categories', $category);

		$categories = $category->getChildCategories();

		if (count($categories) > 0 ) {
			foreach ($categories as $childCategory) {
				$constraints[] = $query->contains('categories', $childCategory);
			}
		}

		$query->matching($query->logicalOr($constraints));

		return $query->execute();
	}

}

?>