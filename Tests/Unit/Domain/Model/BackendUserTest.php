<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\BackendUser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(BackendUser::class)]
final class BackendUserTest extends UnitTestCase
{
    private BackendUser $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new BackendUser();
    }

    #[Test]
    public function checkDefaultValues(): void
    {
        self::assertSame('', $this->subject->getUserName());
        self::assertFalse($this->subject->getIsAdministrator());
        self::assertFalse($this->subject->getIsDisabled());
        self::assertSame('', $this->subject->getEmail());
        self::assertSame('', $this->subject->getRealName());
    }

    #[Test]
    public function setUserNameSetsValue(): void
    {
        $this->subject->setUserName('admin');

        self::assertSame('admin', $this->subject->getUserName());
    }

    #[Test]
    public function setRealNameSetsValue(): void
    {
        $this->subject->setRealName('John Doe');

        self::assertSame('John Doe', $this->subject->getRealName());
    }

    #[Test]
    public function getNameReturnsRealNameWhenSet(): void
    {
        $this->subject->setUserName('admin');
        $this->subject->setRealName('John Doe');

        self::assertSame('John Doe', $this->subject->getName());
    }

    #[Test]
    public function getNameFallsBackToUserNameWhenRealNameIsEmpty(): void
    {
        $this->subject->setUserName('admin');

        self::assertSame('admin', $this->subject->getName());
    }

    #[Test]
    public function getMailToReturnsEmailToNameMapping(): void
    {
        $this->subject->setUserName('admin');
        $this->subject->setRealName('John Doe');
        $this->setProtectedProperty('email', 'felix@example.com');

        self::assertSame(['felix@example.com' => 'John Doe'], $this->subject->getMailTo());
    }

    #[Test]
    public function getMailToUsesUserNameAsFallback(): void
    {
        $this->subject->setUserName('admin');
        $this->setProtectedProperty('email', 'admin@example.com');

        self::assertSame(['admin@example.com' => 'admin'], $this->subject->getMailTo());
    }

    protected function setProtectedProperty(string $property, mixed $value): void
    {
        $reflection = new \ReflectionProperty(BackendUser::class, $property);
        $reflection->setValue($this->subject, $value);
    }
}
