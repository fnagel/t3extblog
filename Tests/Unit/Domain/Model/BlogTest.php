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
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class Tx_T3extblog_Domain_Model_Blog.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage T3Blog Extbase
 *
 */
class Tx_T3extblog_Domain_Model_BlogTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_T3extblog_Domain_Model_Blog
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_T3extblog_Domain_Model_Blog();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getPostsReturnsInitialValueForObjectStorageContainingTx_T3extblog_Domain_Model_Posts() { 
		$newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->fixture->getPosts()
		);
	}

	/**
	 * @test
	 */
	public function setPostsForObjectStorageContainingTx_T3extblog_Domain_Model_PostsSetsPosts() { 
		$post = new Tx_T3extblog_Domain_Model_Posts();
		$objectStorageHoldingExactlyOnePosts = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOnePosts->attach($post);
		$this->fixture->setPosts($objectStorageHoldingExactlyOnePosts);

		$this->assertSame(
			$objectStorageHoldingExactlyOnePosts,
			$this->fixture->getPosts()
		);
	}
	
	/**
	 * @test
	 */
	public function addPostToObjectStorageHoldingPosts() {
		$post = new Tx_T3extblog_Domain_Model_Posts();
		$objectStorageHoldingExactlyOnePost = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOnePost->attach($post);
		$this->fixture->addPost($post);

		$this->assertEquals(
			$objectStorageHoldingExactlyOnePost,
			$this->fixture->getPosts()
		);
	}

	/**
	 * @test
	 */
	public function removePostFromObjectStorageHoldingPosts() {
		$post = new Tx_T3extblog_Domain_Model_Posts();
		$localObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$localObjectStorage->attach($post);
		$localObjectStorage->detach($post);
		$this->fixture->addPost($post);
		$this->fixture->removePost($post);

		$this->assertEquals(
			$localObjectStorage,
			$this->fixture->getPosts()
		);
	}
	
}
?>