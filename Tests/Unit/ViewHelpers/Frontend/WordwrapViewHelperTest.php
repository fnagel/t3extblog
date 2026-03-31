<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\ViewHelpers\Frontend;

use FelixNagel\T3extblog\ViewHelpers\Frontend\WordwrapViewHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(WordwrapViewHelper::class)]
class WordwrapViewHelperTest extends UnitTestCase
{
    #[Test]
    public function renderWrapsTextAtSpecifiedWidth(): void
    {
        $viewHelper = $this->getMockBuilder(WordwrapViewHelper::class)
            ->onlyMethods(['renderChildren'])
            ->getMock();

        $longText = 'This is a very long line of text that should be wrapped at the width boundary.';
        $viewHelper->method('renderChildren')->willReturn($longText);
        $viewHelper->setArguments(['width' => 20]);

        $result = $viewHelper->render();

        self::assertStringContainsString("\n", $result);
        foreach (explode("\n", $result) as $line) {
            self::assertLessThanOrEqual(20, strlen($line));
        }
    }

    #[Test]
    public function renderDoesNotWrapShortText(): void
    {
        $viewHelper = $this->getMockBuilder(WordwrapViewHelper::class)
            ->onlyMethods(['renderChildren'])
            ->getMock();

        $shortText = 'Short text.';
        $viewHelper->method('renderChildren')->willReturn($shortText);
        $viewHelper->setArguments(['width' => 100]);

        $result = $viewHelper->render();

        self::assertStringNotContainsString("\n", $result);
        self::assertSame($shortText, $result);
    }
}
