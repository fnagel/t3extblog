<?php

declare(strict_types=1);

namespace FelixNagel\T3extblog\Tests\Functional\Domain\Repository;

/*
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Category;
use FelixNagel\T3extblog\Domain\Repository\AbstractRepository;
use FelixNagel\T3extblog\Domain\Repository\CategoryRepository;
use FelixNagel\T3extblog\Tests\Functional\AbstractFunctionalTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;

#[CoversClass(CategoryRepository::class)]
#[CoversClass(AbstractRepository::class)]
final class CategoryRepositoryTest extends AbstractFunctionalTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/pages.csv');

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

    protected function getRepository(): CategoryRepository
    {
        return $this->get(CategoryRepository::class);
    }

    #[Test]
    public function findAllReturnsAllCategories(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/categories.csv');

        $results = $this->getRepository()->findAll();

        self::assertCount(4, $results);
    }

    #[Test]
    public function findChildrenReturnsChildCategories(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/categories.csv');

        $category = new Category();
        $category->_setProperty('uid', 1);

        $results = $this->getRepository()->findChildren($category);

        self::assertNotNull($results);
        self::assertCount(2, $results);
    }

    #[Test]
    public function findChildrenReturnsEmptyResultForCategoryWithoutChildren(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/categories.csv');

        $category = new Category();
        $category->_setProperty('uid', 2);

        $results = $this->getRepository()->findChildren($category);

        self::assertNotNull($results);
        self::assertCount(0, $results);
    }
}
