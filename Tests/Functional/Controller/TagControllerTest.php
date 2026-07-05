<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Controller;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;

final class TagControllerTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'Tags');
    }

    #[Test]
    public function cloudActionRendersTagsFromPosts(): void
    {
        if (($GLOBALS['TYPO3_CONF_VARS']['DB']['Connections']['Default']['driver'] ?? '') === 'pdo_sqlite') {
            $this->markTestSkipped('tagCloud() uses SUBSTRING_INDEX which is MySQL/MariaDB-specific.');
        }

        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_tags[controller]', 'Tag')
                ->withQueryParameter('tx_t3extblog_tags[action]', 'cloud')
        );

        $body = (string)$response->getBody();

        self::assertSame(200, $response->getStatusCode());
        // The cloud renders one linked <li> per tag with a "Tag: <title> (<count>)" title.
        self::assertStringContainsString('rel="tag"', $body);
        // typo3 is used by the visible posts 1, 2 and 4; php by the visible posts
        // 1, 4 and 5. The hidden post 3 and the deleted post 6 (both tagged "php")
        // must not be counted — proving the FE enable-field filtering works.
        self::assertStringContainsString('Tag: typo3 (3)', $body);
        self::assertStringContainsString('Tag: php (3)', $body);
    }
}
