<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Hooks;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Doctrine\DBAL\ParameterType;
use FelixNagel\T3extblog\Tests\Functional\Controller\AbstractControllerTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Functional tests for the DataHandler hook (Hooks\Tcemain) that fires when blog
 * records are edited in the backend.
 *
 * Covers two branches:
 * - approving a pending comment triggers the comment notification processing
 *   (CommentNotificationService::processChangedStatus -> notifySubscribers),
 *   which flags the comment as "mails sent";
 * - deleting a post cascades to its related comments and subscribers.
 */
final class TcemainTest extends AbstractControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../Fixtures/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/posts.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/comments.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/post_subscribers.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/tcemain_comments.csv');

        $this->setUpFrontendWithTypoScript();

        // The DataHandler and the hook run in a backend context; grant admin rights
        // so the DataHandler is allowed to modify the blog tables.
        $backendUser = $this->setUpBackendUser(1);
        $backendUser->user['admin'] = 1;
        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)
            ->createFromUserPreferences($GLOBALS['BE_USER']);
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://localhost/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withQueryParams(['id' => 1]);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST'], $GLOBALS['LANG']);
        parent::tearDown();
    }

    #[Test]
    public function approvingCommentTriggersNotificationProcessing(): void
    {
        // Comment 20 is pending on post 4, which has no confirmed subscribers, so the
        // notification run sends no mail but still flags the comment as processed.
        self::assertSame(0, $this->fetchCommentMailsSent(20));

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start(['tx_t3blog_com' => [20 => ['approved' => '1']]], []);
        $dataHandler->process_datamap();

        self::assertSame([], $dataHandler->errorLog, implode("\n", $dataHandler->errorLog));
        self::assertSame(1, $this->fetchCommentMailsSent(20), 'Comment was not processed by the hook.');
    }

    #[Test]
    public function deletingPostCascadesToCommentsAndSubscribers(): void
    {
        self::assertGreaterThan(0, $this->countActive('tx_t3blog_com', 'fk_post', 1));
        self::assertGreaterThan(0, $this->countActive('tx_t3blog_com_nl', 'post_uid', 1));

        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start([], ['tx_t3blog_post' => [1 => ['delete' => 1]]]);
        $dataHandler->process_cmdmap();

        self::assertSame([], $dataHandler->errorLog, implode("\n", $dataHandler->errorLog));
        // The hook deletes all comments and subscriptions belonging to the post.
        self::assertSame(0, $this->countActive('tx_t3blog_com', 'fk_post', 1), 'Comments were not cascade-deleted.');
        self::assertSame(0, $this->countActive('tx_t3blog_com_nl', 'post_uid', 1), 'Subscribers were not cascade-deleted.');
    }

    protected function fetchCommentMailsSent(int $uid): int
    {
        $queryBuilder = $this->getConnectionPool()->getQueryBuilderForTable('tx_t3blog_com');
        $queryBuilder->getRestrictions()->removeAll();

        return (int)$queryBuilder
            ->select('mails_sent')
            ->from('tx_t3blog_com')
            ->where($queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, ParameterType::INTEGER)))
            ->executeQuery()
            ->fetchOne();
    }

    protected function countActive(string $table, string $field, int $postId): int
    {
        $queryBuilder = $this->getConnectionPool()->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();

        return (int)$queryBuilder
            ->count('uid')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq($field, $queryBuilder->createNamedParameter($postId, ParameterType::INTEGER)),
                $queryBuilder->expr()->eq('deleted', 0)
            )
            ->executeQuery()
            ->fetchOne();
    }
}
