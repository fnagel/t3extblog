<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Post;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Post::class)]
final class PostTest extends UnitTestCase
{
    private Post $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new Post();
    }

    // --- tagCloud ---

    #[Test]
    public function getTagCloudReturnsEmptyArrayByDefault(): void
    {
        self::assertSame([], $this->subject->getTagCloud());
    }

    #[Test]
    public function setTagCloudFromStringProducesCorrectArray(): void
    {
        $this->subject->setTagCloud('php, typo3, testing');

        self::assertSame(['php', 'typo3', 'testing'], $this->subject->getTagCloud());
    }

    #[Test]
    public function setTagCloudFromArrayProducesCorrectString(): void
    {
        $this->subject->setTagCloud(['php', 'typo3', 'testing']);

        self::assertSame('php, typo3, testing', $this->subject->getRawTagCloud());
    }

    #[Test]
    public function setTagCloudTrimsWhitespace(): void
    {
        $this->subject->setTagCloud('  php , typo3 ,  testing  ');

        self::assertSame(['php', 'typo3', 'testing'], $this->subject->getTagCloud());
    }

    #[Test]
    public function setTagCloudIgnoresEmptySegments(): void
    {
        $this->subject->setTagCloud('php,,typo3');

        self::assertSame(['php', 'typo3'], $this->subject->getTagCloud());
    }

    // --- isMailSendingAllowed ---

    #[Test]
    public function isMailSendingAllowedReturnsTrueForVisibleUnsentPost(): void
    {
        $this->subject->setMailsSent(false);
        $this->subject->setHidden(false);
        $this->subject->setDeleted(false);

        self::assertTrue($this->subject->isMailSendingAllowed());
    }

    #[Test]
    public function isMailSendingAllowedReturnsFalseWhenMailsAlreadySent(): void
    {
        $this->subject->setMailsSent(true);
        $this->subject->setHidden(false);
        $this->subject->setDeleted(false);

        self::assertFalse($this->subject->isMailSendingAllowed());
    }

    #[Test]
    public function isMailSendingAllowedReturnsFalseForHiddenPost(): void
    {
        $this->subject->setMailsSent(false);
        $this->subject->setHidden(true);
        $this->subject->setDeleted(false);

        self::assertFalse($this->subject->isMailSendingAllowed());
    }

    #[Test]
    public function isMailSendingAllowedReturnsFalseForDeletedPost(): void
    {
        $this->subject->setMailsSent(false);
        $this->subject->setHidden(false);
        $this->subject->setDeleted(true);

        self::assertFalse($this->subject->isMailSendingAllowed());
    }

    // --- isExpired ---

    #[Test]
    public function isExpiredReturnsTrueForOldPost(): void
    {
        $this->subject->setPublishDate(new \DateTime('-2 years'));

        // Default expiry offset is +1 month, so a 2-year-old post is expired
        self::assertTrue($this->subject->isExpired());
    }

    #[Test]
    public function isExpiredReturnsFalseForRecentPost(): void
    {
        $this->subject->setPublishDate(new \DateTime('yesterday'));

        self::assertFalse($this->subject->isExpired());
    }

    #[Test]
    public function isExpiredRespectsCustomExpiryOffset(): void
    {
        $this->subject->setPublishDate(new \DateTime('-6 months'));

        self::assertFalse($this->subject->isExpired('+1 year'));
    }

    // --- getPreview ---

    #[Test]
    public function getPreviewReturnsPreviewTextWhenSet(): void
    {
        $this->subject->setPreviewText('<b>Bold preview</b>');

        self::assertSame('Bold preview', $this->subject->getPreview());
    }

    #[Test]
    public function getPreviewStripsHtmlTags(): void
    {
        $this->subject->setPreviewText('<p>Hello <strong>World</strong></p>');

        self::assertSame('Hello World', $this->subject->getPreview());
    }

    // --- allowComments constants ---

    #[Test]
    #[DataProvider('provideAllowCommentsConstants')]
    public function allowCommentsConstantsHaveExpectedValues(int $constant, int $expected): void
    {
        self::assertSame($expected, $constant);
    }

    public static function provideAllowCommentsConstants(): array
    {
        return [
            'everyone' => [Post::ALLOW_COMMENTS_EVERYONE, 0],
            'nobody'   => [Post::ALLOW_COMMENTS_NOBODY, 1],
            'login'    => [Post::ALLOW_COMMENTS_LOGIN, 2],
        ];
    }
}
