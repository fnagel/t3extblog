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
 * Test case for class Tx_T3extblog_Domain_Model_Comment.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage T3Blog Extbase
 *
 * @author Felix Nagel <info@felixnagel.com>
 */
class Tx_T3extblog_Domain_Model_CommentTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var Tx_T3extblog_Domain_Model_Comment
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new Tx_T3extblog_Domain_Model_Comment();
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
	public function getEmailReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setEmailForStringSetsEmail() { 
		$this->fixture->setEmail('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getEmail()
		);
	}
	
	/**
	 * @test
	 */
	public function getWebsiteReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setWebsiteForStringSetsWebsite() { 
		$this->fixture->setWebsite('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getWebsite()
		);
	}
	
	/**
	 * @test
	 */
	public function getDateReturnsInitialValueForDateTime() { }

	/**
	 * @test
	 */
	public function setDateForDateTimeSetsDate() { }
	
	/**
	 * @test
	 */
	public function getTextReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTextForStringSetsText() { 
		$this->fixture->setText('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getText()
		);
	}
	
	/**
	 * @test
	 */
	public function getApprovedReturnsInitialValueForBoolean() { 
		$this->assertSame(
			TRUE,
			$this->fixture->getApproved()
		);
	}

	/**
	 * @test
	 */
	public function setApprovedForBooleanSetsApproved() { 
		$this->fixture->setApproved(TRUE);

		$this->assertSame(
			TRUE,
			$this->fixture->getApproved()
		);
	}
	
	/**
	 * @test
	 */
	public function getSpamReturnsInitialValueForBoolean() { 
		$this->assertSame(
			TRUE,
			$this->fixture->getSpam()
		);
	}

	/**
	 * @test
	 */
	public function setSpamForBooleanSetsSpam() { 
		$this->fixture->setSpam(TRUE);

		$this->assertSame(
			TRUE,
			$this->fixture->getSpam()
		);
	}
	
}
?>