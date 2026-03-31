<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Category;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Category::class)]
final class CategoryTest extends UnitTestCase
{
    private Category $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Category();
    }

    #[Test]
    public function nameDefaultIsNull(): void
    {
        $reflection = new \ReflectionProperty(Category::class, 'name');
        self::assertNull($reflection->getValue($this->subject));
    }

    #[Test]
    public function descriptionDefaultIsNull(): void
    {
        $reflection = new \ReflectionProperty(Category::class, 'description');
        self::assertNull($reflection->getValue($this->subject));
    }

    #[Test]
    public function setNameSetsValue(): void
    {
        $this->subject->setName('TYPO3');

        self::assertSame('TYPO3', $this->subject->getName());
    }

    #[Test]
    public function setDescriptionSetsValue(): void
    {
        $this->subject->setDescription('Posts about TYPO3');

        self::assertSame('Posts about TYPO3', $this->subject->getDescription());
    }

    #[Test]
    #[DataProvider('provideIsFirstLevelCases')]
    public function isFirstLevelReturnsExpectedResult(?int $parentId, bool $expected): void
    {
        $reflection = new \ReflectionProperty(Category::class, 'parentId');
        $reflection->setValue($this->subject, $parentId);

        self::assertSame($expected, $this->subject->isFirstLevel());
    }

    public static function provideIsFirstLevelCases(): array
    {
        return [
            'null parentId is first level'     => [null, true],
            'zero parentId is first level'     => [0, true],
            'non-zero parentId is not first level' => [5, false],
        ];
    }
}
