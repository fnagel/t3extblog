<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Content;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Content::class)]
final class ContentTest extends UnitTestCase
{
    private Content $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Content();
    }

    #[Test]
    public function checkDefaultValues(): void
    {
        self::assertSame('', $this->subject->getCType());
        self::assertSame('', $this->subject->getHeader());
        self::assertSame('', $this->subject->getBodytext());
        self::assertSame(0, $this->subject->getColPos());
        self::assertFalse($this->subject->getImageZoom());
        self::assertSame('', $this->subject->getLayout());
    }

    #[Test]
    public function setCTypeSetsValue(): void
    {
        $this->subject->setCType('textmedia');

        self::assertSame('textmedia', $this->subject->getCType());
    }

    #[Test]
    public function setHeaderSetsValue(): void
    {
        $this->subject->setHeader('My Header');

        self::assertSame('My Header', $this->subject->getHeader());
    }

    #[Test]
    public function setBodytextSetsValue(): void
    {
        $this->subject->setBodytext('<p>Content here</p>');

        self::assertSame('<p>Content here</p>', $this->subject->getBodytext());
    }

    #[Test]
    public function setColPosSetsValue(): void
    {
        $this->subject->setColPos(2);

        self::assertSame(2, $this->subject->getColPos());
    }

    #[Test]
    public function setImageZoomSetsValue(): void
    {
        $this->subject->setImageZoom(true);

        self::assertTrue($this->subject->getImageZoom());
    }

    #[Test]
    public function setLayoutSetsValue(): void
    {
        $this->subject->setLayout('1');

        self::assertSame('1', $this->subject->getLayout());
    }
}
