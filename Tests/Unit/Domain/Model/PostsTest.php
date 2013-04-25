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
 * Test case for class Tx_T3extblog_Domain_Model_Posts.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage T3Blog Extbase
 *
 */
class Tx_T3extblog_Domain_Model_PostsTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_T3extblog_Domain_Model_Posts
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_T3extblog_Domain_Model_Posts();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() { 
		$this->fixture->setTitle('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getTitle()
		);
	}
	
	/**
	 * @test
	 */
	public function getAuthorReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setAuthorForStringSetsAuthor() { 
		$this->fixture->setAuthor('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getAuthor()
		);
	}
	
	/**
	 * @test
	 */
	public function getPublishDateReturnsInitialValueForDateTime() { }

	/**
	 * @test
	 */
	public function setPublishDateForDateTimeSetsPublishDate() { }
	
	/**
	 * @test
	 */
	public function getAllowCommentsReturnsInitialValueForBoolean() { 
		$this->assertSame(
			TRUE,
			$this->fixture->getAllowComments()
		);
	}

	/**
	 * @test
	 */
	public function setAllowCommentsForBooleanSetsAllowComments() { 
		$this->fixture->setAllowComments(TRUE);

		$this->assertSame(
			TRUE,
			$this->fixture->getAllowComments()
		);
	}
	
	/**
	 * @test
	 */
	public function getTagCloudReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTagCloudForStringSetsTagCloud() { 
		$this->fixture->setTagCloud('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getTagCloud()
		);
	}
	
	/**
	 * @test
	 */
	public function getNumberOfViewsReturnsInitialValueForInteger() { 
		$this->assertSame(
			0,
			$this->fixture->getNumberOfViews()
		);
	}

	/**
	 * @test
	 */
	public function setNumberOfViewsForIntegerSetsNumberOfViews() { 
		$this->fixture->setNumberOfViews(12);

		$this->assertSame(
			12,
			$this->fixture->getNumberOfViews()
		);
	}
	
	/**
	 * @test
	 */
	public function getContentReturnsInitialValueForObjectStorageContainingTx_T3extblog_Domain_Model_Content() { 
		$newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->fixture->getContent()
		);
	}

	/**
	 * @test
	 */
	public function setContentForObjectStorageContainingTx_T3extblog_Domain_Model_ContentSetsContent() { 
		$content = new Tx_T3extblog_Domain_Model_Content();
		$objectStorageHoldingExactlyOneContent = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneContent->attach($content);
		$this->fixture->setContent($objectStorageHoldingExactlyOneContent);

		$this->assertSame(
			$objectStorageHoldingExactlyOneContent,
			$this->fixture->getContent()
		);
	}
	
	/**
	 * @test
	 */
	public function addContentToObjectStorageHoldingContent() {
		$content = new Tx_T3extblog_Domain_Model_Content();
		$objectStorageHoldingExactlyOneContent = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneContent->attach($content);
		$this->fixture->addContent($content);

		$this->assertEquals(
			$objectStorageHoldingExactlyOneContent,
			$this->fixture->getContent()
		);
	}

	/**
	 * @test
	 */
	public function removeContentFromObjectStorageHoldingContent() {
		$content = new Tx_T3extblog_Domain_Model_Content();
		$localObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$localObjectStorage->attach($content);
		$localObjectStorage->detach($content);
		$this->fixture->addContent($content);
		$this->fixture->removeContent($content);

		$this->assertEquals(
			$localObjectStorage,
			$this->fixture->getContent()
		);
	}
	
	/**
	 * @test
	 */
	public function getCategoryReturnsInitialValueForObjectStorageContainingTx_T3extblog_Domain_Model_Category() { 
		$newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->fixture->getCategory()
		);
	}

	/**
	 * @test
	 */
	public function setCategoryForObjectStorageContainingTx_T3extblog_Domain_Model_CategorySetsCategory() { 
		$category = new Tx_T3extblog_Domain_Model_Category();
		$objectStorageHoldingExactlyOneCategory = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneCategory->attach($category);
		$this->fixture->setCategory($objectStorageHoldingExactlyOneCategory);

		$this->assertSame(
			$objectStorageHoldingExactlyOneCategory,
			$this->fixture->getCategory()
		);
	}
	
	/**
	 * @test
	 */
	public function addCategoryToObjectStorageHoldingCategory() {
		$category = new Tx_T3extblog_Domain_Model_Category();
		$objectStorageHoldingExactlyOneCategory = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneCategory->attach($category);
		$this->fixture->addCategory($category);

		$this->assertEquals(
			$objectStorageHoldingExactlyOneCategory,
			$this->fixture->getCategory()
		);
	}

	/**
	 * @test
	 */
	public function removeCategoryFromObjectStorageHoldingCategory() {
		$category = new Tx_T3extblog_Domain_Model_Category();
		$localObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$localObjectStorage->attach($category);
		$localObjectStorage->detach($category);
		$this->fixture->addCategory($category);
		$this->fixture->removeCategory($category);

		$this->assertEquals(
			$localObjectStorage,
			$this->fixture->getCategory()
		);
	}
	
	/**
	 * @test
	 */
	public function getCommentsReturnsInitialValueForObjectStorageContainingTx_T3extblog_Domain_Model_Comment() { 
		$newObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$this->assertEquals(
			$newObjectStorage,
			$this->fixture->getComments()
		);
	}

	/**
	 * @test
	 */
	public function setCommentsForObjectStorageContainingTx_T3extblog_Domain_Model_CommentSetsComments() { 
		$comment = new Tx_T3extblog_Domain_Model_Comment();
		$objectStorageHoldingExactlyOneComments = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneComments->attach($comment);
		$this->fixture->setComments($objectStorageHoldingExactlyOneComments);

		$this->assertSame(
			$objectStorageHoldingExactlyOneComments,
			$this->fixture->getComments()
		);
	}
	
	/**
	 * @test
	 */
	public function addCommentToObjectStorageHoldingComments() {
		$comment = new Tx_T3extblog_Domain_Model_Comment();
		$objectStorageHoldingExactlyOneComment = new Tx_Extbase_Persistence_ObjectStorage();
		$objectStorageHoldingExactlyOneComment->attach($comment);
		$this->fixture->addComment($comment);

		$this->assertEquals(
			$objectStorageHoldingExactlyOneComment,
			$this->fixture->getComments()
		);
	}

	/**
	 * @test
	 */
	public function removeCommentFromObjectStorageHoldingComments() {
		$comment = new Tx_T3extblog_Domain_Model_Comment();
		$localObjectStorage = new Tx_Extbase_Persistence_ObjectStorage();
		$localObjectStorage->attach($comment);
		$localObjectStorage->detach($comment);
		$this->fixture->addComment($comment);
		$this->fixture->removeComment($comment);

		$this->assertEquals(
			$localObjectStorage,
			$this->fixture->getComments()
		);
	}
	
}
?>