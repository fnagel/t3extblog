<?php

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * BaseTest.
 */
abstract class BaseTest extends UnitTestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    protected $fixture;

    /**
     */
    public function setUp()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     */
    public function tearDown()
    {
        unset($this->fixture);
    }
}
