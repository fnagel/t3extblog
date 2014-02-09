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
 * Test case for class Tx_T3extblog_Domain_Model_Subscriber
 *
 * @package TYPO3
 * @subpackage T3Blog Extbase
 *
 */
class Tx_T3extblog_Domain_Model_SubscriberTest extends BaseTest {

	/**
	 * @var Tx_T3extblog_Domain_Model_Subscriber
	 */
	protected $fixture;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->fixture = $this->objectManager->create('Tx_T3extblog_Domain_Model_Subscriber', 123);

		$this->fixture->setName('John Doe');
		$this->fixture->setEmail('test@domain.com');
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function testCanGetPostUid() {
		$this->assertEquals(
			123,
			$this->fixture->getPostUid()
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function testCreateCode() {
		$this->assertNull($this->fixture->getCode());

		$this->fixture->updateAuth();
		$code = $this->fixture->getCode();
		$timestamp = $this->fixture->getLastSent();

		$this->assertNotEmpty($code);
		$this->assertNotEmpty($timestamp);

		sleep(1);
		$this->fixture->updateAuth();

		$this->assertNotEquals(
			$code,
			$this->fixture->getCode()
		);
		$this->assertNotEquals(
			$timestamp,
			$this->fixture->getLastSent()
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function testIsAuthCodeExpired() {
		$this->fixture->setLastSent(new DateTime('now'));
		$this->fixture->getLastSent()->modify('-2 weeks');

		$this->assertTrue(
			$this->fixture->isAuthCodeExpired('+1 weeks')
		);

		$this->assertFalse(
			$this->fixture->isAuthCodeExpired('+3 weeks')
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function testCanGetMailTo() {
		$this->assertEquals(
			$this->fixture->getMailTo(),
			array(
				'test@domain.com' => 'John Doe'
			)
		);
	}
}
?>