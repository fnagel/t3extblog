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

/**
 * BlogSubscriberRepository
 */
class BlogSubscriberRepository extends AbstractSubscriberRepository {

	/**
	 * Search for already registered subscriptions
	 *
	 * @param string $email
	 * @param integer $excludeUid
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
	 */
	public function findExistingSubscriptions($email, $excludeUid = NULL) {
		$query = $this->createQuery();

		$constraints = $this->getBasicExistingSubscriptionConstraints($query, $email, $excludeUid);

		$query->matching(
			$query->logicalAnd($constraints)
		);

		return $query->execute();
	}

	/**
	 * Finds a single subscriber without opt-in mail sent before
	 *
	 * @param string $email
	 *
	 * @return object
	 */
	public function findForSubscriptionMail($email) {
		$query = $this->createQuery();
		$query->getQuerySettings()->setIgnoreEnableFields(TRUE);

		$constraints = $this->getBasicForSubscriptionMailConstraints($query, $email);

		$query->matching(
			$query->logicalAnd($constraints)
		);

		return $query->execute()->getFirst();
	}
}
