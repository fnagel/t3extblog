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
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
        //
        // The request is passed ONLY to the ConfigurationManager, NOT into
        // $GLOBALS['TYPO3_REQUEST']: Typo3QuerySettings derives its ignoreEnableFields
        // default from the *global* request, which stays unset here. This keeps the
        // repository queries behaving like frontend queries (enable fields respected)
        // since TYPO3 v14.3.6, where a global backend request would make queries include
        // hidden records by default.
        $this->get(ConfigurationManagerInterface::class)->setRequest(
            (new ServerRequest('https://localhost/'))
                ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
                ->withQueryParams(['id' => 1])
        );
    }

    protected function getPost(int $uid, bool $respectEnableFields = true): Post
    {
        return $this->get(PostRepository::class)->findByUid($uid, $respectEnableFields);
    }
}
