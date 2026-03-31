<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Domain\Model;

use FelixNagel\T3extblog\Domain\Model\PostSubscriber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(PostSubscriber::class)]
class PostSubscriberTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'testEncryptionKey';
        parent::setUp();
        $this->subject = new PostSubscriber(1);
        $this->subject->setEmail('test@example.com');
    }

    #[Test]
    public function isValidForOptinReturnsFalseWhenNotHidden(): void
    {
        $this->subject->setHidden(false);

        self::assertFalse($this->subject->isValidForOptin());
    }

    #[Test]
    public function isValidForOptinReturnsFalseWhenLastSentIsSet(): void
    {
        $this->subject->setHidden(true);
        $this->subject->setLastSent(new \DateTime());

        self::assertFalse($this->subject->isValidForOptin());
    }

    #[Test]
    public function updateAuthSetsLastSentToNow(): void
    {
        self::assertNull($this->subject->getLastSent());

        $before = new \DateTime();
        $this->subject->updateAuth();
        $after = new \DateTime();

        self::assertNotNull($this->subject->getLastSent());
        self::assertGreaterThanOrEqual($before, $this->subject->getLastSent());
        self::assertLessThanOrEqual($after, $this->subject->getLastSent());
    }

    #[Test]
    public function updateAuthSetsCode(): void
    {
        $this->subject->updateAuth();

        self::assertIsString($this->subject->getCode());
        self::assertSame(32, strlen($this->subject->getCode()));
    }

    #[Test]
    public function isAuthCodeExpiredReturnsTrueForOldLastSent(): void
    {
        $this->subject->setLastSent(new \DateTime('-72 hours'));

        self::assertTrue($this->subject->isAuthCodeExpired('+48 hours'));
    }

    #[Test]
    public function isAuthCodeExpiredReturnsFalseForRecentLastSent(): void
    {
        $this->subject->setLastSent(new \DateTime('-24 hours'));

        self::assertFalse($this->subject->isAuthCodeExpired('+48 hours'));
    }

    #[Test]
    public function getMailToReturnsEmailNameArray(): void
    {
        $this->subject->setName('Test Name');

        $result = $this->subject->getMailTo();

        self::assertIsArray($result);
        self::assertArrayHasKey('test@example.com', $result);
        self::assertSame('Test Name', $result['test@example.com']);
    }
}
