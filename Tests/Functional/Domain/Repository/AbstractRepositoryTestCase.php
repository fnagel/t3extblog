<?php

declare(strict_types=1);

namespace FelixNagel\t3extblog\Tests\Functional\Domain\Repository;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use FelixNagel\T3extblog\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;

abstract class AbstractRepositoryTestCase extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/pages.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/posts.csv');

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/categories.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/post_categories_mm.csv');

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/comments.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/post_subscribers.csv');

        // Provide a backend request with page id=1 so Extbase resolves storagePid=1
        // (matching the pid used in shared fixture CSV files).
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://localhost/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withQueryParams(['id' => 1]);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
        parent::tearDown();
    }

    protected function getPost(int $uid, bool $respectEnableFields = true): Post
    {
        return $this->get(PostRepository::class)->findByUid($uid, $respectEnableFields);
    }
}
