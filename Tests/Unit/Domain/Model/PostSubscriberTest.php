<?php

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\PostSubscriber;

/**
 * Test case for class Subscriber.
 */
class PostSubscriberTest extends BaseTest
{
    /**
     * @var PostSubscriber
     */
    protected $fixture;

    /**
     */
    public function setUp()
    {
        parent::setUp();
        $this->fixture = new PostSubscriber(123);

        $this->fixture->setName('John Doe');
        $this->fixture->setEmail('test@domain.com');
    }

    /**
     * @test
     */
    public function testCanGetPostUid()
    {
        $this->assertEquals(
            123,
            $this->fixture->getPostUid()
        );
    }

    /**
     * @test
     */
    public function testCreateCode()
    {
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
     */
    public function testIsAuthCodeExpired()
    {
        $this->fixture->setLastSent(new \DateTime('now'));
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
     */
    public function testCanGetMailTo()
    {
        $this->assertEquals(
            $this->fixture->getMailTo(),
            [
                'test@domain.com' => 'John Doe',
            ]
        );
    }
}
