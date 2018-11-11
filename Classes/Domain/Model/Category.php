<?php

namespace FelixNagel\T3extblog\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

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
        if ($this->parentId) {
            return false;
        }

        return true;
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
