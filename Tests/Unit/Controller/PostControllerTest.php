<?php

namespace TYPO3\T3extblog\Tests\Unit\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2017 Felix Nagel <info@felixnagel.com>
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

use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\T3extblog\Domain\Model\Post;

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
