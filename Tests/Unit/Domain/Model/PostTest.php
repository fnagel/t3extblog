<?php

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
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

use FelixNagel\T3extblog\Domain\Model\Post;

/**
 * Test case for class Post.
 */
class PostTest extends BaseTest
{
    /**
     * @var Post
     */
    protected $fixture;

    /**
     */
    public function setUp()
    {
        parent::setUp();

        $this->fixture = new Post();
    }

    /**
     * @test
     */
    public function testPublishDateMethods()
    {
        $this->fixture->setPublishDate(new \DateTime('2014-01-14'));

        $this->assertSame(
            '2014',
            $this->fixture->getPublishYear()
        );

        $this->assertSame(
            '01',
            $this->fixture->getPublishMonth()
        );

        $this->assertSame(
            '14',
            $this->fixture->getPublishDay()
        );
    }

    /**
     * @test
     */
    public function testIsExpired()
    {
        $this->fixture->setPublishDate(new \DateTime('now'));
        $this->fixture->getPublishDate()->modify('-6 months');

        $this->assertTrue(
            $this->fixture->isExpired('+5 months')
        );

        $this->assertFalse(
            $this->fixture->isExpired('+7 months')
        );
    }

    /**
     * @test
     */
    public function testTagCloudMethods()
    {
        $tagCloud = ['tag1', 'tag2', 'tag3'];

        $this->fixture->setTagCloud($tagCloud);
        $this->assertEquals(
            $this->fixture->getTagCloud(),
            $tagCloud
        );

        $this->fixture->setTagCloud(' tag1 ,tag2,tag3');
        $this->assertEquals(
            $this->fixture->getTagCloud(),
            $tagCloud
        );
    }

    /**
     * @test
     *
     * @todo
     */
    public function testCanGetContentIdList()
    {
        $this->markTestSkipped('to be written');
    }

    /**
     * @test
     *
     * @todo
     */
    public function testCanGetPreview()
    {
        $this->markTestSkipped('to be written');
    }

    /**
     * @test
     *
     * @todo
     */
    public function testCanGetComments()
    {
        $this->markTestSkipped('to be written');
    }

    /**
     * @test
     */
    public function testCanGetLinkParameter()
    {
        $this->fixture->setPublishDate(new \DateTime('2014-01-14'));
        $this->fixture->_setProperty('uid', 123);

        $this->assertEquals(
            $this->fixture->getLinkParameter(),
            [
                'post' => '123',
                'day' => '14',
                'month' => '01',
                'year' => '2014',
            ]
        );
    }
}
