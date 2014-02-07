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
 * Test case for class Tx_T3extblog_Domain_Model_Posts.
 *
 * @package TYPO3
 * @subpackage T3Blog Extbase
 *
 */
class Tx_T3extblog_Domain_Model_CommentTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var Tx_T3extblog_Domain_Model_Comment
	 */
	protected $fixture;

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 *
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = $this->objectManager->create('Tx_T3extblog_Domain_Model_Comment');

		$this->fixture->_setProperty('hidden', FALSE);
		$this->fixture->_setProperty('deleted', FALSE);
	}

	/**
	 * @return void
	 */
	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function testIsValidAfterInit() {
		$this->assertFalse(
			$this->fixture->isValid()
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function testIsValid() {
		$this->fixture->setApproved(TRUE);
		$this->fixture->setSpam(FALSE);

		$this->assertTrue(
			$this->fixture->isValid()
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function testIsValidHidden() {
		$this->fixture->setApproved(TRUE);
		$this->fixture->setSpam(FALSE);
		$this->fixture->_setProperty('hidden', TRUE);

		$this->assertFalse(
			$this->fixture->isValid()
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function testIsValidUnapproved() {
		$this->fixture->setApproved(FALSE);
		$this->fixture->setSpam(FALSE);

		$this->assertFalse(
			$this->fixture->isValid()
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function testIsValidSpam() {
		$this->fixture->setApproved(TRUE);
		$this->fixture->setSpam(TRUE);

		$this->assertFalse(
			$this->fixture->isValid()
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function testIsValidUnapprovedAndSpam() {
		$this->fixture->setApproved(FALSE);
		$this->fixture->setSpam(TRUE);

		$this->assertFalse(
			$this->fixture->isValid()
		);
	}


	/**
	 * @test
	 *
	 * @todo
	 *
	 * @return void
	 */
	public function testCanGetPost() {
		$this->markTestSkipped("to be written");
	}

}
?>