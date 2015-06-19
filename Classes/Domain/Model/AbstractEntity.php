<?php

namespace TYPO3\T3extblog\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2015 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity as CoreAbstractEntity;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
abstract class AbstractEntity extends CoreAbstractEntity {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
	 */
	protected $objectManager = NULL;

	/**
	 * commentRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\CommentRepository
	 */
	protected $commentRepository = NULL;

	/**
	 * postRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\PostRepository
	 */
	protected $postRepository = NULL;

	/**
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 *
	 * @return void
	 */
	public function injectObjectManager(ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Get commentRepository
	 *
	 * @return \TYPO3\T3extblog\Domain\Repository\CommentRepository
	 */
	protected function getCommentRepository() {
		if ($this->commentRepository === NULL) {
			$this->commentRepository = $this->objectManager->get('TYPO3\\T3extblog\\Domain\\Repository\\CommentRepository');
		}

		return $this->commentRepository;
	}

	/**
	 * Get postRepository
	 *
	 * @return \TYPO3\T3extblog\Domain\Repository\PostRepository
	 */
	protected function getPostRepository() {
		if ($this->postRepository === NULL) {
			$this->postRepository = $this->objectManager->get('TYPO3\\T3extblog\\Domain\\Repository\\PostRepository');
		}

		return $this->postRepository;
	}

	/**
	 * Makes an array out of all public getter methods
	 *
	 * @param boolean $camelCaseKeys If set to false the array keys are TYPO3 cObj compatible
	 *
	 * @return array
	 */
	public function toArray($camelCaseKeys = FALSE) {
		$camelCaseProperties = ObjectAccess::getGettableProperties($this);

		if ($camelCaseKeys === TRUE) {
			return $camelCaseProperties;
		}

		$data = array();
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
	public function __sleep() {
		$properties = get_object_vars($this);

		// fix to make sure we are able to use forward in controller
		unset($properties['commentRepository']);
		unset($properties['commentRepository']);

		return array_keys($properties);
	}
}
