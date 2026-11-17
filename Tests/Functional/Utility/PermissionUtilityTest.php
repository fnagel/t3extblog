<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Utility;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Exception\AccessDeniedException;
use FelixNagel\T3extblog\Tests\Functional\AbstractFunctionalTestCase;
use FelixNagel\T3extblog\Utility\PermissionUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * Page tree of the fixture:
 *
 * - 1 Blog Root (mounted for the editor)
 *   - 2 Blog Storage
 *   - 4 Restricted Storage (no permissions for everybody)
 * - 3 Other Root (not mounted for the editor)
 *
 * Users: 1 = admin, 2 = editor (mount on page 1), 3 = editor without mounts,
 * 4 = editor without table permissions (mount on page 1)
 *
 * The editor group allows reading posts and modifying comments.
 */
final class PermissionUtilityTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Permissions/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Permissions/be_users.csv');
    }

    public static function pageAccessDataProvider(): array
    {
        return [
            'admin: blog root' => [1, 1, true],
            'admin: sub page' => [1, 2, true],
            'admin: other root' => [1, 3, true],
            'admin: restricted page' => [1, 4, true],
            'admin: non existing page' => [1, 99, false],
            'admin: root level' => [1, 0, false],
            'admin: negative page id' => [1, -1, false],
            'editor: mounted page' => [2, 1, true],
            'editor: sub page of mounted page' => [2, 2, true],
            'editor: page outside of mounts' => [2, 3, false],
            'editor: page without permissions' => [2, 4, false],
            'editor: negative page id' => [2, -1, false],
            'editor without mounts: blog root' => [3, 1, false],
            'editor without mounts: sub page' => [3, 2, false],
        ];
    }

    #[Test]
    #[DataProvider('pageAccessDataProvider')]
    public function hasPageAccessRespectsMountsAndPagePermissions(int $userUid, int $pageId, bool $expected): void
    {
        $this->setUpBackendUser($userUid);

        self::assertSame($expected, PermissionUtility::hasPageAccess($pageId));
    }

    #[Test]
    public function hasPageAccessReturnsFalseWithoutBackendUser(): void
    {
        unset($GLOBALS['BE_USER']);

        self::assertFalse(PermissionUtility::hasPageAccess(1));
    }

    #[Test]
    public function getPageReturnsPageRecordForAccessiblePage(): void
    {
        $this->setUpBackendUser(2);

        $page = PermissionUtility::getPage(2);

        self::assertIsArray($page);
        self::assertSame(2, (int)$page['uid']);
    }

    #[Test]
    public function getPageReturnsFalseForInaccessiblePage(): void
    {
        $this->setUpBackendUser(2);

        self::assertFalse(PermissionUtility::getPage(3));
    }

    public static function tableReadAccessDataProvider(): array
    {
        return [
            'admin: posts' => [1, 'tx_t3blog_post', true],
            'admin: post subscribers' => [1, 'tx_t3blog_com_nl', true],
            'editor: posts' => [2, 'tx_t3blog_post', true],
            // Modify access includes read access
            'editor: comments (modify only)' => [2, 'tx_t3blog_com', true],
            'editor: post subscribers' => [2, 'tx_t3blog_com_nl', false],
            'editor: blog subscribers' => [2, 'tx_t3blog_blog_nl', false],
            'editor without table permissions: posts' => [4, 'tx_t3blog_post', false],
            'editor without table permissions: comments' => [4, 'tx_t3blog_com', false],
        ];
    }

    public static function tableModifyAccessDataProvider(): array
    {
        return [
            'admin: posts' => [1, 'tx_t3blog_post', true],
            'admin: comments' => [1, 'tx_t3blog_com', true],
            'editor: posts (read only)' => [2, 'tx_t3blog_post', false],
            'editor: comments' => [2, 'tx_t3blog_com', true],
            'editor without table permissions: comments' => [4, 'tx_t3blog_com', false],
        ];
    }

    #[Test]
    #[DataProvider('tableModifyAccessDataProvider')]
    public function hasTableModifyAccessRespectsTablePermissions(int $userUid, string $table, bool $expected): void
    {
        $this->setUpBackendUser($userUid);

        self::assertSame($expected, PermissionUtility::hasTableModifyAccess($table));
    }

    #[Test]
    public function hasTableModifyAccessReturnsFalseWithoutBackendUser(): void
    {
        unset($GLOBALS['BE_USER']);

        self::assertFalse(PermissionUtility::hasTableModifyAccess('tx_t3blog_post'));
    }

    #[Test]
    #[DataProvider('tableReadAccessDataProvider')]
    public function hasTableReadAccessRespectsTablePermissions(int $userUid, string $table, bool $expected): void
    {
        $this->setUpBackendUser($userUid);

        self::assertSame($expected, PermissionUtility::hasTableReadAccess($table));
    }

    #[Test]
    public function hasTableReadAccessReturnsFalseWithoutBackendUser(): void
    {
        unset($GLOBALS['BE_USER']);

        self::assertFalse(PermissionUtility::hasTableReadAccess('tx_t3blog_post'));
    }

    #[Test]
    public function assertPageAccessPassesForAccessiblePage(): void
    {
        $this->setUpBackendUser(2);

        PermissionUtility::assertPageAccess(2);

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function assertPageAccessThrowsExceptionWithHint(): void
    {
        $this->setUpBackendUser(2);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionCode(1794816001);
        $this->expectExceptionMessage(PermissionUtility::ACCESS_DENIED_HINT);

        PermissionUtility::assertPageAccess(3);
    }

    #[Test]
    public function assertTableAccessPassesForReadableTables(): void
    {
        $this->setUpBackendUser(2);

        PermissionUtility::assertTableAccess(['tx_t3blog_post', 'tx_t3blog_com']);
        PermissionUtility::assertTableAccess(['tx_t3blog_com'], true);

        $this->addToAssertionCount(1);
    }

    #[Test]
    public function assertTableAccessThrowsExceptionWithHint(): void
    {
        $this->setUpBackendUser(2);

        $this->expectException(AccessDeniedException::class);
        $this->expectExceptionCode(1794816003);
        $this->expectExceptionMessage('Access to table tx_t3blog_com_nl denied. ' . PermissionUtility::ACCESS_DENIED_HINT);

        PermissionUtility::assertTableAccess(['tx_t3blog_post', 'tx_t3blog_com_nl']);
    }

    #[Test]
    public function assertTableAccessThrowsExceptionWithoutModifyAccess(): void
    {
        $this->setUpBackendUser(2);

        $this->expectException(AccessDeniedException::class);

        PermissionUtility::assertTableAccess(['tx_t3blog_post'], true);
    }

    #[Test]
    public function filterAccessiblePagesFiltersAndReindexesPageUids(): void
    {
        $this->setUpBackendUser(2);

        // Page UIDs from database queries might be strings
        self::assertSame([1, '2'], PermissionUtility::filterAccessiblePages([1, '2', 3, 4]));
    }

    #[Test]
    public function filterAccessiblePagesFiltersPageRecords(): void
    {
        $this->setUpBackendUser(2);

        $pages = [
            ['uid' => 3, 'title' => 'Other Root'],
            ['uid' => 2, 'title' => 'Blog Storage'],
            ['uid' => 4, 'title' => 'Restricted Storage'],
        ];

        self::assertSame([['uid' => 2, 'title' => 'Blog Storage']], PermissionUtility::filterAccessiblePages($pages));
    }

    #[Test]
    public function filterAccessiblePagesReturnsEmptyArrayWithoutAccess(): void
    {
        $this->setUpBackendUser(3);

        self::assertSame([], PermissionUtility::filterAccessiblePages([1, 2, 3, 4]));
    }
}
