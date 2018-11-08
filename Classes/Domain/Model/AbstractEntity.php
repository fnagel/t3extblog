<?php

namespace FelixNagel\T3extblog\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2018 Felix Nagel <info@felixnagel.com>
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

use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity as CoreAbstractEntity;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * AbstractEntity.
 */
abstract class AbstractEntity extends CoreAbstractEntity
{
    /**
     * Creation date and time.
     *
     * @var \DateTime
     */
    protected $crdate;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $objectManager = null;

    /**
     * commentRepository.
     *
     * @var \FelixNagel\T3extblog\Domain\Repository\CommentRepository
     */
    protected $commentRepository = null;

    /**
     * postRepository.
     *
     * @var \FelixNagel\T3extblog\Domain\Repository\PostRepository
     */
    protected $postRepository = null;

    /**
     * @return \DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->getCrdate();
    }

    /**
     * @param $crdate
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * Get commentRepository.
     *
     * @return CommentRepository
     */
    protected function getCommentRepository()
    {
        if ($this->commentRepository === null) {
            $this->commentRepository = $this->objectManager->get(CommentRepository::class);
        }

        return $this->commentRepository;
    }

    /**
     * Get postRepository.
     *
     * @return PostRepository
     */
    protected function getPostRepository()
    {
        if ($this->postRepository === null) {
            $this->postRepository = $this->objectManager->get(PostRepository::class);
        }

        return $this->postRepository;
    }

    /**
     * Makes an array out of all public getter methods.
     *
     * @param bool $camelCaseKeys If set to false the array keys are TYPO3 cObj compatible
     *
     * @return array
     */
    public function toArray($camelCaseKeys = false)
    {
        $camelCaseProperties = ObjectAccess::getGettableProperties($this);

        if ($camelCaseKeys === true) {
            return $camelCaseProperties;
        }

        $data = [];
        foreach ($camelCaseProperties as $camelCaseFieldKey => $value) {
            $fieldKey = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $camelCaseFieldKey));

            // TYPO3 cObj edge case
            if ($camelCaseFieldKey === 'cType') {
                $fieldKey = ucfirst($camelCaseFieldKey);
            }

            $data[$fieldKey] = $value;
        }

        return $data;
    }

    /**
     * Serialization (sleep) helper.
     *
     * @return array Names of the properties to be serialized
     */
    public function __sleep()
    {
        $properties = get_object_vars($this);

        // fix to make sure we are able to use forward in controller
        unset($properties['postRepository']);
        unset($properties['commentRepository']);

        return array_keys($properties);
    }
}
