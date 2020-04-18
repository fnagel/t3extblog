<?php

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
                'post' => 123,
            ]
        );
    }
}
