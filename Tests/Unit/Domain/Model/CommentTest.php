<?php

namespace TYPO3\T3extblog\Tests\Unit\Domain\Model;

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

/**
 * Test case for class Comment
 */
class CommentTest extends BaseTest {

	/**
	 * @var Comment
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		parent::setUp();

		$this->fixture = new Comment();
		$this->fixture->_setProperty('uid', 123);

		$this->fixture->_setProperty('hidden', FALSE);
		$this->fixture->_setProperty('deleted', FALSE);
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
