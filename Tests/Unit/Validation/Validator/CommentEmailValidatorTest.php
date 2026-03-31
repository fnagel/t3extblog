<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Validation\Validator;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Service\SettingsService;
use FelixNagel\T3extblog\Validation\Validator\CommentEmailValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Exception\InvalidValidationOptionsException;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(CommentEmailValidator::class)]
final class CommentEmailValidatorTest extends UnitTestCase
{
    private CommentEmailValidator $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new CommentEmailValidator([]);
    }

    protected function tearDown(): void
    {
        GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[Test]
    public function nonCommentValueThrowsException(): void
    {
        $this->expectException(InvalidValidationOptionsException::class);
        $this->expectExceptionCode(1592253083);

        $this->subject->validate('not a comment object');
    }

    #[Test]
    public function emptyEmailWithRequiredEmailAddsError(): void
    {
        $this->registerSettingsMock('blogsystem.comments.requireEmail', true);

        $comment = new Comment();
        $comment->setEmail('');

        $result = $this->subject->validate($comment);

        self::assertTrue($result->hasErrors());
        self::assertNotEmpty($result->forProperty('email')->getErrors());
    }

    #[Test]
    public function emptyEmailWithoutRequirementPassesValidation(): void
    {
        // Two getConfiguration() calls happen: requireEmail check, then subscribeForComments check.
        // addInstance() is consumed once per makeInstance(), so register the mock twice.
        $settingsMock = $this->createMock(SettingsService::class);
        $settingsMock->method('getTypoScriptByPath')->willReturnMap([
            ['blogsystem.comments.requireEmail', false],
            ['blogsystem.comments.subscribeForComments', false],
        ]);
        GeneralUtility::addInstance(SettingsService::class, $settingsMock);
        GeneralUtility::addInstance(SettingsService::class, $settingsMock);

        $comment = new Comment();
        $comment->setEmail('');

        $result = $this->subject->validate($comment);

        self::assertFalse($result->hasErrors());
    }

    #[Test]
    public function emptyEmailRequiredForSubscriptionAddsError(): void
    {
        // Two getConfiguration() calls happen: requireEmail check, then subscribeForComments check.
        // addInstance() is consumed once per makeInstance(), so register the mock twice.
        $settingsMock = $this->createMock(SettingsService::class);
        $settingsMock->method('getTypoScriptByPath')
            ->willReturnMap([
                ['blogsystem.comments.requireEmail', false],
                ['blogsystem.comments.subscribeForComments', true],
            ]);
        GeneralUtility::addInstance(SettingsService::class, $settingsMock);
        GeneralUtility::addInstance(SettingsService::class, $settingsMock);

        $comment = new Comment();
        $comment->setEmail('');
        $comment->setSubscribe(true);

        $result = $this->subject->validate($comment);

        self::assertTrue($result->hasErrors());
        self::assertNotEmpty($result->forProperty('email')->getErrors());
    }

    #[Test]
    public function filledEmailAlwaysPassesValidation(): void
    {
        $this->registerSettingsMock('blogsystem.comments.requireEmail', true);

        $comment = new Comment();
        $comment->setEmail('user@example.com');

        $result = $this->subject->validate($comment);

        self::assertFalse($result->hasErrors());
    }

    protected function registerSettingsMock(string $path, mixed $returnValue): void
    {
        /** @var SettingsService&MockObject $settingsMock */
        $settingsMock = $this->createMock(SettingsService::class);
        $settingsMock->method('getTypoScriptByPath')->with($path)->willReturn($returnValue);
        GeneralUtility::addInstance(SettingsService::class, $settingsMock);
    }
}
