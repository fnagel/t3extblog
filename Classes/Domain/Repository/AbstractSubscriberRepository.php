<?php

namespace TYPO3\T3extblog\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\T3extblog\Domain\Model\AbstractSubscriber;

/**
 * AbstractSubscriberRepository
 */
abstract class AbstractSubscriberRepository extends AbstractRepository {

	protected $defaultOrderings = array(
		'crdate' => QueryInterface::ORDER_DESCENDING
	);

	/**
	 * Find by code
	 *
	 * @param string $code
	 * @param boolean $enableFields
	 *
	 * @return AbstractSubscriber
	 */
	public function findByCode($code, $enableFields = TRUE) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(!$enableFields);

		$query->matching(
			$query->logicalAnd(
				$query->equals('code', $code),
				$query->equals('deleted', 0)
			)
		);

		return $query->execute()->getFirst();
	}

	/**
	 * @param QueryInterface $query
	 * @param string $email
	 * @param integer $excludeUid
	 *
	 * @return array
	 */
	protected function getBasicExistingSubscriptionConstraints(QueryInterface $query, $email, $excludeUid = NULL) {
		$constraints = array();

		$constraints[] = $query->equals('email', $email);

		if ($excludeUid !== NULL) {
			$constraints[] = $query->logicalNot($query->equals('uid', intval($excludeUid)));
		}

		return $constraints;
	}

}
