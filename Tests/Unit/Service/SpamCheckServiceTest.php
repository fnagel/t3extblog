<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Service;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Service\SpamCheckService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(SpamCheckService::class)]
final class SpamCheckServiceTest extends UnitTestCase
{
    private SpamCheckService $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new SpamCheckService(
            $this->createMock(EventDispatcherInterface::class)
        );
    }

    // --- checkHoneyPotFields ---

    #[Test]
    public function honeypotPassesWhenAllFieldsAreEmptyAndTimestampIsCorrect(): void
    {
        $result = $this->callCheckHoneyPotFields([
            'author'    => '',
            'link'      => '',
            'text'      => '',
            'timestamp' => '1368283172',
        ]);

        self::assertTrue($result);
    }

    #[Test]
    public function honeypotFailsWhenAuthorFieldIsFilled(): void
    {
        $result = $this->callCheckHoneyPotFields([
            'author'    => 'Bot Name',
            'link'      => '',
            'text'      => '',
            'timestamp' => '1368283172',
        ]);

        self::assertFalse($result);
    }

    #[Test]
    public function honeypotFailsWhenLinkFieldIsFilled(): void
    {
        $result = $this->callCheckHoneyPotFields([
            'author'    => '',
            'link'      => 'http://spam.example.com',
            'text'      => '',
            'timestamp' => '1368283172',
        ]);

        self::assertFalse($result);
    }

    #[Test]
    public function honeypotFailsWhenTextFieldIsFilled(): void
    {
        $result = $this->callCheckHoneyPotFields([
            'author'    => '',
            'link'      => '',
            'text'      => 'Spam text',
            'timestamp' => '1368283172',
        ]);

        self::assertFalse($result);
    }

    #[Test]
    public function honeypotFailsWhenTimestampIsWrong(): void
    {
        $result = $this->callCheckHoneyPotFields([
            'author'    => '',
            'link'      => '',
            'text'      => '',
            'timestamp' => '9999999999',
        ]);

        self::assertFalse($result);
    }

    #[Test]
    public function honeypotFailsWhenAuthorFieldIsMissing(): void
    {
        $result = $this->callCheckHoneyPotFields([
            'link'      => '',
            'text'      => '',
            'timestamp' => '1368283172',
        ]);

        self::assertFalse($result);
    }

    // --- checkTextForLinks ---

    #[Test]
    #[DataProvider('provideTextsWithLinks')]
    public function textWithLinkIsDetected(string $text): void
    {
        $result = $this->callCheckTextForLinks($text);

        self::assertTrue($result);
    }

    public static function provideTextsWithLinks(): array
    {
        return [
            'http url'    => ['Check out http://example.com for more'],
            'https url'   => ['Visit https://example.com today'],
            'bare scheme' => ['See ftp://files.example.com'],
        ];
    }

    #[Test]
    #[DataProvider('provideTextsWithoutLinks')]
    public function textWithoutLinkIsNotDetected(string $text): void
    {
        $result = $this->callCheckTextForLinks($text);

        self::assertFalse($result);
    }

    public static function provideTextsWithoutLinks(): array
    {
        return [
            'plain text'        => ['Just a normal comment without any links'],
            'empty string'      => [''],
            'email address'     => ['Contact me at user@example.com'],
        ];
    }

    // --- helpers ---

    protected function callCheckHoneyPotFields(array $arguments): bool
    {
        $method = new \ReflectionMethod($this->subject, 'checkHoneyPotFields');
        return $method->invoke($this->subject, $arguments);
    }

    protected function callCheckTextForLinks(string $text): bool
    {
        $method = new \ReflectionMethod($this->subject, 'checkTextForLinks');
        return $method->invoke($this->subject, $text);
    }
}
