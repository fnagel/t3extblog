<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\CategoryRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use FelixNagel\T3extblog\Domain\Model\Category;
use Psr\Http\Message\ResponseInterface;

/**
 * CategoryController.
 */
class CategoryController extends AbstractController
{
    public function __construct(protected CategoryRepository $categoryRepository, protected PostRepository $postRepository)
    {
    }

    public function listAction(): ResponseInterface
    {
        $categories = $this->categoryRepository->findAll();

        // Add basic PID based cache tag
        // @extensionScannerIgnoreLine
        $this->addCacheTags($categories->getFirst());

        $this->view->assign('categories', $categories);

        return $this->htmlResponse();
    }

    public function showAction(Category $category, int $page = 1): ResponseInterface
    {
        // @extensionScannerIgnoreLine
        $this->addCacheTags($category);
        $this->view->assign('category', $category);

        return $this->paginationHtmlResponse(
            $this->postRepository->findByCategory($category),
            $this->settings['categories']['paginate'],
            $page
        );
    }
}
