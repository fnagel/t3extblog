<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Controller;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Exception\AccessDeniedException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequestContext;

/**
 * Preview of hidden posts (post 3 "Draft Post" on page 1).
 *
 * See PermissionUtilityTest for the page tree and backend users.
 *
 * The backend user handler of the testing framework does not fetch the group data (mounts, table
 * permissions) of the backend user in frontend requests, so non-admin users never have access here.
 * The permission checks itself are covered by PermissionUtilityTest.
 */
final class PostPreviewTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Permissions/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Permissions/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/categories.csv');

        $this->setUpFrontendWithTypoScript();
    }

    #[Test]
    public function previewShowsHiddenPostForAdmin(): void
    {
        $response = $this->executeFrontendSubRequest(
            $this->buildPreviewRequest(),
            (new InternalRequestContext())->withBackendUserId(1)
        );

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('Draft Post', (string)$response->getBody());
    }

    public static function previewDeniedDataProvider(): array
    {
        // Both were allowed before, as any logged-in backend user could preview hidden posts
        return [
            'editor without access to page' => [3],
            'editor without permissions for post table' => [4],
        ];
    }

    #[Test]
    #[DataProvider('previewDeniedDataProvider')]
    public function previewIsDeniedForBackendUserWithoutAccess(int $backendUserUid): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->executeFrontendSubRequest(
            $this->buildPreviewRequest(),
            (new InternalRequestContext())->withBackendUserId($backendUserUid)
        );
    }

    #[Test]
    public function previewIsDeniedWithoutBackendUser(): void
    {
        $this->expectException(AccessDeniedException::class);

        $this->executeFrontendSubRequest($this->buildPreviewRequest());
    }

    protected function buildPreviewRequest(): InternalRequest
    {
        return (new InternalRequest())
            ->withQueryParameter('tx_t3extblog_blogsystem[controller]', 'Post')
            ->withQueryParameter('tx_t3extblog_blogsystem[action]', 'preview')
            ->withQueryParameter('tx_t3extblog_blogsystem[previewPost]', '3');
    }
}
