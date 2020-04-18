<?php

namespace FelixNagel\T3extblog\Tests\Unit\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use FelixNagel\T3extblog\Domain\Model\Post;

/**
 * Test case for class PostController.
 */
class PostControllerTest extends UnitTestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Post
     */
    protected $fixture;

    /**
     */
    public function setUp()
    {
        $this->fixture = new Post();
    }

    /**
     */
    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     *
     * @todo
     */
    public function testFindByTagOrCategory()
    {
        $this->markTestSkipped('to be written');
    }
}
