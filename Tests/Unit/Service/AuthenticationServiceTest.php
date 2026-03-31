<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Service;

use FelixNagel\T3extblog\Service\AuthenticationService;
use FelixNagel\T3extblog\Service\SessionServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(AuthenticationService::class)]
class AuthenticationServiceTest extends UnitTestCase
{
    protected AuthenticationService $subject;
    protected SessionServiceInterface $sessionMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionMock = $this->createMock(SessionServiceInterface::class);
        $this->subject = new AuthenticationService($this->sessionMock);
    }

    #[Test]
    public function isValidReturnsFalseWithEmptySession(): void
    {
        $this->sessionMock->method('getData')->willReturn(null);

        self::assertFalse($this->subject->isValid());
    }

    #[Test]
    public function isValidReturnsFalseWithEmptyEmailInSession(): void
    {
        $this->sessionMock->method('getData')->willReturn(['email' => '']);

        self::assertFalse($this->subject->isValid());
    }

    #[Test]
    public function isValidReturnsTrueWithEmailInSession(): void
    {
        $this->sessionMock->method('getData')->willReturn(['email' => 'test@example.com']);

        self::assertTrue($this->subject->isValid());
    }

    #[Test]
    public function loginCallsSetDataWithEmail(): void
    {
        $this->sessionMock->expects(self::once())
            ->method('setData')
            ->with(['email' => 'test@example.com']);

        $result = $this->subject->login('test@example.com');

        self::assertTrue($result);
    }

    #[Test]
    public function logoutCallsRemoveData(): void
    {
        $this->sessionMock->expects(self::once())
            ->method('removeData');

        $this->subject->logout();
    }
}
