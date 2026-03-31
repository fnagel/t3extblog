<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Comment;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Comment::class)]
final class CommentTest extends UnitTestCase
{
    private Comment $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Comment();
    }

    #[Test]
    #[DataProvider('provideIsValidCases')]
    public function isValidReturnsExpectedResult(
        bool $spam,
        bool $approved,
        bool $hidden,
        bool $deleted,
        bool $expected
    ): void {
        $this->subject->setSpam($spam);
        $this->subject->setApproved($approved);
        $this->subject->setHidden($hidden);
        $this->subject->setDeleted($deleted);

        self::assertSame($expected, $this->subject->isValid());
    }

    public static function provideIsValidCases(): array
    {
        return [
            'valid: approved, not spam, not hidden, not deleted' => [false, true, false, false, true],
            'invalid: is spam'                                   => [true, true, false, false, false],
            'invalid: not approved'                              => [false, false, false, false, false],
            'invalid: hidden'                                    => [false, true, true, false, false],
            'invalid: deleted'                                   => [false, true, false, true, false],
            'invalid: spam and not approved'                     => [true, false, false, false, false],
            'invalid: spam and hidden'                           => [true, true, true, false, false],
            'invalid: all flags active'                          => [true, false, true, true, false],
        ];
    }

    #[Test]
    public function getPlainTextStripsHtmlTags(): void
    {
        $this->subject->setText('<p>Hello <strong onclick="">World</strong>!</p>');

        self::assertSame('Hello World!', $this->subject->getPlainText());
    }

    #[Test]
    public function getPlainTextPreservesTextWithoutTags(): void
    {
        $this->subject->setText('Plain text comment without any tags');

        self::assertSame('Plain text comment without any tags', $this->subject->getPlainText());
    }

    #[Test]
    public function getPlainTextHandlesNestedTags(): void
    {
        $this->subject->setText('<div><p>Nested <em>tags</em> here</p></div>');

        self::assertSame('Nested tags here', $this->subject->getPlainText());
    }

    #[Test]
    public function markAsSpamSetsSpamFlag(): void
    {
        self::assertFalse($this->subject->isSpam());

        $this->subject->markAsSpam();

        self::assertTrue($this->subject->isSpam());
    }

    #[Test]
    public function markAsSpamDoesNotChangeApprovedFlag(): void
    {
        $this->subject->setApproved(true);

        $this->subject->markAsSpam();

        self::assertTrue($this->subject->isApproved(), 'markAsSpam() must not touch the approved flag');
    }

    #[Test]
    public function newCommentIsNotSpamByDefault(): void
    {
        self::assertFalse($this->subject->isSpam());
    }

    #[Test]
    public function newCommentIsNotApprovedByDefault(): void
    {
        self::assertFalse($this->subject->isApproved());
    }

    #[Test]
    public function newCommentIsInvalidByDefault(): void
    {
        // New comments are not approved, so isValid() must return false
        self::assertFalse($this->subject->isValid());
    }

    #[Test]
    public function subscribeDefaultIsFalse(): void
    {
        self::assertFalse($this->subject->getSubscribe());
    }

    #[Test]
    public function mailsSentDefaultIsFalse(): void
    {
        self::assertFalse($this->subject->getMailsSent());
    }
}
