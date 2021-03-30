<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\CategoryRepository;
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
     * @Extbase\Validate("NotEmpty")
     */
    protected $name;

    /**
     * description.
     *
     * @var string
     */
    protected $description;

    /**
     * Id of parent category.
     *
     * @var int
     */
    protected $parentId;

    /**
     * Posts.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Post>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $posts = null;

    /**
     * child categories.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FelixNagel\T3extblog\Domain\Model\Category>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $childCategories = null;

    /**
     * Returns the name.
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the description.
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * If category is first level.
     *
     * @return bool
     */
    public function isFirstLevel()
    {
        return !$this->parentId;
    }

    /**
     * Returns all matching posts.
     *
     * @return ObjectStorage $posts
     */
    public function getPosts()
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
     *
     * @return null|ObjectStorage $posts
     */
    public function getChildCategories()
    {
        if (!$this->isFirstLevel()) {
            return null;
        }

        if ($this->childCategories === null) {
            /* @var $categoryRepository CategoryRepository */
            $categoryRepository = $this->objectManager->get(CategoryRepository::class);
            $categories = $categoryRepository->findChildren($this);

            $this->childCategories = new ObjectStorage();
            foreach ($categories as $category) {
                $this->childCategories->attach($category);
            }
        }

        return $this->childCategories;
    }
}
