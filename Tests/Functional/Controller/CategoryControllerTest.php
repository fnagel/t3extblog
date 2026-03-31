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

final class CategoryControllerTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/categories.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');

        $this->setUpFrontendWithTypoScript(1, 'T3extblog', 'Categories');
    }

    #[Test]
    public function listActionReturns200(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_categories[controller]', 'Category')
                ->withQueryParameter('tx_t3extblog_categories[action]', 'list')
        );

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function listActionRendersCategories(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_categories[controller]', 'Category')
                ->withQueryParameter('tx_t3extblog_categories[action]', 'list')
        );

        self::assertStringContainsString('PHP', (string)$response->getBody());
    }

    #[Test]
    public function showActionReturns200ForExistingCategory(): void
    {
        $response = $this->executeFrontendSubRequest(
            (new InternalRequest())
                ->withQueryParameter('tx_t3extblog_categories[controller]', 'Category')
                ->withQueryParameter('tx_t3extblog_categories[action]', 'show')
                ->withQueryParameter('tx_t3extblog_categories[category]', '1')
        );

        self::assertSame(200, $response->getStatusCode());
    }
}
