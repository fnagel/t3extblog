<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Dashboard;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Dashboard\Provider\LatestCommentListDataProvider;
use FelixNagel\T3extblog\Dashboard\Provider\PendingCommentNumberWithIconDataProvider;
use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Exception\AccessDeniedException;
use FelixNagel\T3extblog\Tests\Functional\AbstractFunctionalTestCase;
use FelixNagel\T3extblog\Utility\BlogPageSearchUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Dashboard widgets must only show records of blog pages the backend user has access to. Without
 * access to any blog page or to the table, an exception is thrown (misconfigured permissions).
 *
 * See PermissionUtilityTest for the page tree and users. Pending comments are stored on
 * page 2 (2x, accessible for the editor), page 3 (outside of the editor mounts) and
 * page 4 (no page permissions for the editor).
 */
final class DataProviderPermissionTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Permissions/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Permissions/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/Permissions/comments.csv');

        // Blog pages are cached statically, make sure each test uses its own database state
        $this->resetStaticProperty(BlogPageSearchUtility::class, 'cache');
        $this->resetStaticProperty(BlogPageSearchUtility::class, 'connectionPool');

        $this->get(ConfigurationManagerInterface::class)->setRequest(
            (new ServerRequest('https://localhost/typo3/'))
                ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
        );
    }

    public static function pendingCommentsDataProvider(): array
    {
        return [
            'admin sees comments of all blog pages' => [1, [1, 2, 3, 4]],
            'editor only sees comments of accessible blog pages' => [2, [1, 2]],
        ];
    }

    public static function accessDeniedDataProvider(): array
    {
        return [
            'editor without mounts' => [3],
            'editor with page access but without table permissions' => [4],
        ];
    }

    #[Test]
    #[DataProvider('accessDeniedDataProvider')]
    public function pendingCommentNumberThrowsExceptionWithoutAccess(int $userUid): void
    {
        $this->setUpBackendUser($userUid);
        $this->expectException(AccessDeniedException::class);

        (new PendingCommentNumberWithIconDataProvider($this->get(CommentRepository::class)))->getNumber();
    }

    #[Test]
    #[DataProvider('accessDeniedDataProvider')]
    public function latestCommentListThrowsExceptionWithoutAccess(int $userUid): void
    {
        $this->setUpBackendUser($userUid);
        $this->expectException(AccessDeniedException::class);

        (new LatestCommentListDataProvider($this->get(CommentRepository::class)))->getItems();
    }

    #[Test]
    #[DataProvider('pendingCommentsDataProvider')]
    public function pendingCommentNumberRespectsPageAccess(int $userUid, array $expectedCommentUids): void
    {
        $this->setUpBackendUser($userUid);

        $provider = new PendingCommentNumberWithIconDataProvider($this->get(CommentRepository::class));

        self::assertSame(count($expectedCommentUids), $provider->getNumber());
    }

    #[Test]
    #[DataProvider('pendingCommentsDataProvider')]
    public function latestCommentListRespectsPageAccess(int $userUid, array $expectedCommentUids): void
    {
        $this->setUpBackendUser($userUid);

        $provider = new LatestCommentListDataProvider($this->get(CommentRepository::class));
        $commentUids = array_map(static fn (Comment $comment): int => $comment->getUid(), $provider->getItems());
        sort($commentUids);

        self::assertSame($expectedCommentUids, $commentUids);
    }

    private function resetStaticProperty(string $className, string $propertyName): void
    {
        (new \ReflectionProperty($className, $propertyName))->setValue(null, null);
    }
}
