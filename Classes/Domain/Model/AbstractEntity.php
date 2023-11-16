<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\AbstractRepository;
use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity as CoreAbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage;

/**
 * AbstractEntity.
 */
abstract class AbstractEntity extends CoreAbstractEntity
{
    /**
     * Creation date and time.
     */
    protected \DateTime $crdate;

    protected ?CommentRepository $commentRepository = null;

    protected ?PostRepository $postRepository = null;

    public function getCrdate(): \DateTime
    {
        return $this->crdate;
    }

    public function getCreateDate(): \DateTime
    {
        return $this->getCrdate();
    }

    public function setCrdate(\DateTime $crdate): void
    {
        $this->crdate = $crdate;
    }

    protected function getCommentRepository(): CommentRepository
    {
        if ($this->commentRepository === null) {
            $this->commentRepository = GeneralUtility::makeInstance(CommentRepository::class);
        }

        return $this->commentRepository;
    }

    protected function getPostRepository(): PostRepository
    {
        if ($this->postRepository === null) {
            $this->postRepository = GeneralUtility::makeInstance(PostRepository::class);
        }

        return $this->postRepository;
    }

    /**
     * Makes an array out of all public getter methods.
     *
     * @param bool $camelCaseKeys If set to false the array keys are TYPO3 cObj compatible
     */
    public function toArray(bool $camelCaseKeys = false): array
    {
        $camelCaseProperties = ObjectAccess::getGettableProperties($this);

        if ($camelCaseKeys) {
            return $camelCaseProperties;
        }

        $data = [];
        foreach ($camelCaseProperties as $camelCaseFieldKey => $value) {
            $fieldKey = strtolower(preg_replace('#([a-z])([A-Z])#', '$1_$2', $camelCaseFieldKey));

            // TYPO3 cObj edge case
            if ($camelCaseFieldKey === 'cType') {
                $fieldKey = ucfirst($camelCaseFieldKey);
            }

            $data[$fieldKey] = $value;
        }

        return $data;
    }

    protected function loadLazyRelation($relation): void
    {
        if ($relation instanceof LazyLoadingProxy) {
            $relation->_loadRealInstance();
        }
    }

    /**
     * Serialization (sleep) helper.
     *
     * @return array Names of the properties to be serialized
     */
    public function __sleep(): array
    {
        return array_keys($this->getPropertiesForSerialization());
    }

    /**
     * @return array Names of the properties to be serialized
     */
    protected function getPropertiesForSerialization(): array
    {
        $properties = get_object_vars($this);

        // Remove properties not required if fully populated
        unset(
            $properties['objectManager'],
            $properties['postRepository'],
            $properties['commentRepository'],
            $properties['rawComments']
        );

        // Remove lazy object storage as this will break post preview when serializing the post in form VH
        foreach ($properties as $key => $property) {
            if ($property instanceof LazyObjectStorage
                || $property instanceof AbstractRepository
                || $property instanceof QueryResult
            ) {
                unset($properties[$key]);
            }
        }

        return $properties;
    }
}
