<?php

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Comment;

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
