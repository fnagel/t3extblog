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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(CategoryRepository::class)]
#[CoversClass(AbstractRepository::class)]
final class CategoryRepositoryTest extends AbstractRepositoryTestCase
{
    protected function getRepository(): CategoryRepository
    {
        return $this->get(CategoryRepository::class);
    }

    #[Test]
    public function findAllReturnsAllCategories(): void
    {
        $results = $this->getRepository()->findAll();

        self::assertCount(4, $results);
    }

    #[Test]
    public function findChildrenReturnsChildCategories(): void
    {
        $category = new Category();
        $category->_setProperty('uid', 1);

        $results = $this->getRepository()->findChildren($category);

        self::assertNotNull($results);
        self::assertCount(2, $results);
    }

    #[Test]
    public function findChildrenReturnsEmptyResultForCategoryWithoutChildren(): void
    {
        $category = new Category();
        $category->_setProperty('uid', 2);

        $results = $this->getRepository()->findChildren($category);

        self::assertNotNull($results);
        self::assertCount(0, $results);
    }
}
