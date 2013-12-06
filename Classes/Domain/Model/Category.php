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
class Tx_T3extblog_Domain_Model_Category extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * name
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $name;

	/**
	 * description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Id of parent category
	 *
	 * @var integer
	 */
	protected $parentId;

	/**
	 * Posts
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Post>
	 * @lazy
	 */
	protected $posts = NULL;

	/**
	 * child categories
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Category>
	 * @lazy
	 */
	protected $childCategories = NULL;

	/**
	 * Returns the name
	 *
	 * @return string $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the name
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 *
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}


	/**
	 * If category is first level
	 *
	 * @return boolean
	 */
	public function isFirstLevel() {
		if ($this->parentId) {
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * Returns all matching posts
	 *
	 * @return $posts
	 */
	public function getPosts() {
		if ($this->posts == NULL) {
			$this->posts = t3lib_div::makeInstance("Tx_T3extblog_Domain_Repository_PostRepository")->findByCategory($this);
		}

		return $this->posts;
	}

	/**
	 * Returns all child categories
	 *
	 * @return $posts
	 */
	public function getChildCategories() {
		if (!$this->isFirstLevel()) {
			return FALSE;
		}

		if ($this->childCategories == NULL) {
			$this->childCategories = t3lib_div::makeInstance("Tx_T3extblog_Domain_Repository_CategoryRepository")->findByParentId($this->getUid());
		}

		return $this->childCategories;
	}

}

?>