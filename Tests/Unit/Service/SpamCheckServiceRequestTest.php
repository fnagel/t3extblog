<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Service;

use FelixNagel\T3extblog\Event\SpamCheckEvent;
use FelixNagel\T3extblog\Service\SpamCheckService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(SpamCheckService::class)]
class SpamCheckServiceRequestTest extends UnitTestCase
{
    protected SpamCheckService $subject;
    protected EventDispatcherInterface $eventDispatcherMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->eventDispatcherMock->method('dispatch')
            ->willReturnCallback(function (SpamCheckEvent $event) {
                return $event;
            });

        $this->subject = new SpamCheckService($this->eventDispatcherMock);

        $request = new ServerRequest('http://example.com/', 'POST');
        $request = $request
            ->withQueryParams(['tx_t3extblog' => []])
            ->withParsedBody(['tx_t3extblog' => [], 'tx_t3extblog_blogsystem' => []]);
        $GLOBALS['TYPO3_REQUEST'] = $request;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        parent::tearDown();
    }

    #[Test]
    public function processReturnsZeroWhenSpamCheckDisabled(): void
    {
        $result = $this->subject->process(['enable' => false]);

        self::assertSame(0, $result);
    }

    #[Test]
    public function processDispatchesSpamCheckEvent(): void
    {
        $this->eventDispatcherMock->expects(self::once())->method('dispatch');

        $result = $this->subject->process([
            'enable' => true,
            'honeypot' => 0,
            'isHumanCheckbox' => 0,
            'cookie' => 0,
            'userAgent' => 0,
            'link' => 0,
        ]);

        self::assertSame(0, $result);
    }

    #[Test]
    public function processAddsPointsForMissingHumanCheckbox(): void
    {
        $result = $this->subject->process([
            'enable' => true,
            'honeypot' => 0,
            'isHumanCheckbox' => 5,
            'cookie' => 0,
            'userAgent' => 0,
            'link' => 0,
        ]);

        self::assertSame(5, $result);
    }
}
