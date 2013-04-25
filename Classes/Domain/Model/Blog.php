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
class Tx_T3extblog_Domain_Model_Blog extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * posts
	 *
	 * @var Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Posts>
	 * @lazy
	 */
	protected $posts;

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		/**
		 * Do not modify this method!
		 * It will be rewritten on each save in the extension builder
		 * You may modify the constructor of this class instead
		 */
		$this->posts = new Tx_Extbase_Persistence_ObjectStorage();
	}

	/**
	 * Adds a Posts
	 *
	 * @param Tx_T3extblog_Domain_Model_Posts $post
	 * @return void
	 */
	public function addPost(Tx_T3extblog_Domain_Model_Posts $post) {
		$this->posts->attach($post);
	}

	/**
	 * Removes a Posts
	 *
	 * @param Tx_T3extblog_Domain_Model_Posts $postToRemove The Posts to be removed
	 * @return void
	 */
	public function removePost(Tx_T3extblog_Domain_Model_Posts $postToRemove) {
		$this->posts->detach($postToRemove);
	}

	/**
	 * Returns the posts
	 *
	 * @return Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Posts> $posts
	 */
	public function getPosts() {
		return $this->posts;
	}

	/**
	 * Sets the posts
	 *
	 * @param Tx_Extbase_Persistence_ObjectStorage<Tx_T3extblog_Domain_Model_Posts> $posts
	 * @return void
	 */
	public function setPosts(Tx_Extbase_Persistence_ObjectStorage $posts) {
		$this->posts = $posts;
	}

}
?>