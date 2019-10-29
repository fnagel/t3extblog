<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Domain\Model\Category;

/**
 * CategoryController.
 */
class CategoryController extends AbstractController
{
    /**
     * @var array
     */
    protected $cHashActions = [
        'showAction',
    ];

    /**
     * categoryRepository.
     *
     * @var \FelixNagel\T3extblog\Domain\Repository\CategoryRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $categoryRepository;

    /**
     * action list.
     */
    public function listAction()
    {
        $categories = $this->categoryRepository->findAll();

        // Add basic PID based cache tag
        $this->addCacheTags($categories->getFirst());

        $this->view->assign('categories', $categories);
    }

    /**
     * action show.
     *
     * @param Category $category
     */
    public function showAction(Category $category)
    {
        $this->addCacheTags($category);
        $this->view->assign('category', $category);
    }
}
