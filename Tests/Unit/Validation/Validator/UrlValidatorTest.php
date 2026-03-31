<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Unit\Validation\Validator;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Validation\Validator\UrlValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(UrlValidator::class)]
final class UrlValidatorTest extends UnitTestCase
{
    private UrlValidator $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new UrlValidator();
    }

    #[Test]
    #[DataProvider('provideUrls')]
    public function invalidUrlAddsValidationError(string $url, bool $valid): void
    {
        $result = $this->subject->validate($url);

        if ($valid) {
            self::assertFalse($result->hasErrors());
        } else {
            self::assertTrue($result->hasErrors());
        }
    }

    public static function provideUrls(): array
    {
        return [
            'valid'              => ['http://www.example.com', true],
            'valid SSL'          => ['https://www.example.com', true],
            'valid with query'   => ['http://example.com/path?query=value#anchor', true],
            'empty'              => ['', true],
            'mailto scheme'      => ['mailto:user@example.com', false],
            'ftp scheme'         => ['ftp://example.com', false],
            'no scheme'          => ['example.com', false],
            'plain text'         => ['not a url at all', false],
            'javascript scheme'  => ['javascript:alert(1)', false],
        ];
    }
}
