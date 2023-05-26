<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use FelixNagel\T3extblog\Domain\Repository\CategoryRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * Category.
 */
class Category extends AbstractLocalizedEntity
{
    /**
     * name.
     *
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    protected ?string $name = null;

    /**
     * description.
     *
     * @var string
     */
    protected ?string $description = null;

    /**
     * Id of parent category.
     */
    protected ?int $parentId = null;

    /**
     * Posts.
     *
     * @var ObjectStorage<Post>
     */
    #[Lazy]
    protected ?ObjectStorage $posts = null;

    /**
     * child categories.
     *
     * @var ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Category>
     */
    #[Lazy]
    protected ?ObjectStorage $childCategories = null;

    /**
     * Returns the name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name.
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Returns the description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the description.
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * If category is first level.
     */
    public function isFirstLevel(): bool
    {
        return !$this->parentId;
    }

    /**
     * Returns all matching posts.
     *
     * @return ObjectStorage $posts
     */
    public function getPosts(): ObjectStorage
    {
        if ($this->posts === null) {
            $posts = $this->getPostRepository()->findByCategory($this);

            $this->posts = new ObjectStorage();
            foreach ($posts as $post) {
                $this->posts->attach($post);
            }
        }

        return $this->posts;
    }

    /**
     * Returns all child categories.
     */
    public function getChildCategories(): ?ObjectStorage
    {
        if (!$this->isFirstLevel()) {
            return null;
        }

        if ($this->childCategories === null) {
            /* @var $categoryRepository CategoryRepository */
            $categoryRepository = GeneralUtility::makeInstance(CategoryRepository::class);
            $categories = $categoryRepository->findChildren($this);

            $this->childCategories = new ObjectStorage();
            foreach ($categories as $category) {
                $this->childCategories->attach($category);
            }
        }

        return $this->childCategories;
    }
}
