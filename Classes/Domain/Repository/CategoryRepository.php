<?php

namespace FelixNagel\T3extblog\Domain\Repository;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * CategoryRepository.
 */
class CategoryRepository extends AbstractRepository
{
    protected $defaultOrderings = [
        'sorting' => QueryInterface::ORDER_ASCENDING,
        'name' => QueryInterface::ORDER_ASCENDING,
    ];

    /**
     * Returns all children of the given category
     *
     * @param \FelixNagel\T3extblog\Domain\Model\Category $category
     *
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findChildren($category)
    {
        if (!$category->isFirstLevel()) {
            return null;
        }

        $query = $this->createQuery();
        $query->matching(
            $query->equals('parent_id', $category->getUid())
        );

        return $query->execute();
    }
}
