<?php

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

/**
 *
 *
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
abstract class Tx_T3extblog_Domain_Model_AbstractEntity extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * commentRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_CommentRepository
	 */
	protected $commentRepository = NULL;

	/**
	 * postRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_PostRepository
	 */
	protected $postRepository = NULL;

	/**
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 *
	 * @return void
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Get commentRepository
	 *
	 * @vreturn Tx_T3extblog_Domain_Repository_CommentRepository
	 */
	protected function getCommentRepository() {
		if ($this->commentRepository === NULL) {
			$this->commentRepository = $this->objectManager->get('Tx_T3extblog_Domain_Repository_CommentRepository');
		}

		return $this->commentRepository;
	}

	/**
	 * Get postRepository
	 *
	 * @vreturn Tx_T3extblog_Domain_Repository_PostRepository
	 */
	protected function getPostRepository() {
		if ($this->postRepository === NULL) {
			$this->postRepository = $this->objectManager->get('Tx_T3extblog_Domain_Repository_PostRepository');
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
		$camelCaseProperties = Tx_Extbase_Reflection_ObjectAccess::getGettableProperties($this);

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

?>