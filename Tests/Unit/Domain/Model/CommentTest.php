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
 * Test case for class Comment.
 */
class CommentTest extends BaseTest
{
    /**
     * @var Comment
     */
    protected $fixture;

    /**
     */
    public function setUp()
    {
        parent::setUp();

        $this->fixture = new Comment();
        $this->fixture->_setProperty('uid', 123);

        $this->fixture->_setProperty('hidden', false);
        $this->fixture->_setProperty('deleted', false);
    }

    /**
     * @test
     */
    public function testIsValidAfterInit()
    {
        $this->assertFalse(
            $this->fixture->isValid()
        );
    }

    /**
     * @test
     */
    public function testIsValid()
    {
        $this->fixture->setApproved(true);
        $this->fixture->setSpam(false);

        $this->assertTrue(
            $this->fixture->isValid()
        );
    }

    /**
     * @test
     */
    public function testIsValidHidden()
    {
        $this->fixture->setApproved(true);
        $this->fixture->setSpam(false);
        $this->fixture->_setProperty('hidden', true);

        $this->assertFalse(
            $this->fixture->isValid()
        );
    }

    /**
     * @test
     */
    public function testIsValidUnapproved()
    {
        $this->fixture->setApproved(false);
        $this->fixture->setSpam(false);

        $this->assertFalse(
            $this->fixture->isValid()
        );
    }

    /**
     * @test
     */
    public function testIsValidSpam()
    {
        $this->fixture->setApproved(true);
        $this->fixture->setSpam(true);

        $this->assertFalse(
            $this->fixture->isValid()
        );
    }

    /**
     * @test
     */
    public function testIsValidUnapprovedAndSpam()
    {
        $this->fixture->setApproved(false);
        $this->fixture->setSpam(true);

        $this->assertFalse(
            $this->fixture->isValid()
        );
    }

    /**
     * @test
     *
     * @todo
     */
    public function testCanGetPost()
    {
        $this->markTestSkipped('to be written');
    }
}
