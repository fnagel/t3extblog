<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\Category;
use FelixNagel\T3extblog\Domain\Repository\CategoryRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Utility\MathUtility;

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
        $category = null;

        /* @var $routing PageArguments */
        $routing = $this->request->getAttribute('routing');
        if (($categoryUid = $this->isCategoryArguments($routing->getArguments())) !== false) {
            $category = $this->categoryRepository->findByUid($categoryUid);
        }

        // Add basic PID based cache tag
        // @extensionScannerIgnoreLine
        $this->addCacheTags($categories->getFirst());

        $this->view->assignMultiple([
            'categories' => $categories,
            'currentCategory' => $category,
        ]);

        return $this->htmlResponse();
    }

    protected function isCategoryArguments(array $arguments): int|false
    {
        return isset(
            $arguments['tx_t3extblog_blogsystem']['controller'],
            $arguments['tx_t3extblog_blogsystem']['action'],
            $arguments['tx_t3extblog_blogsystem']['category'],
        ) &&
            $arguments['tx_t3extblog_blogsystem']['controller'] === 'Post' &&
            $arguments['tx_t3extblog_blogsystem']['action'] === 'category' &&
            MathUtility::canBeInterpretedAsInteger($arguments['tx_t3extblog_blogsystem']['category']) ?
            (int)$arguments['tx_t3extblog_blogsystem']['category'] : false;
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
