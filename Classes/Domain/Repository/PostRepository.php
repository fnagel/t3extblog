<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Felix Nagel <info@felixnagel.com>
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
class Tx_T3extblog_Domain_Repository_PostRepository extends Tx_Extbase_Persistence_Repository {

	protected $defaultOrderings = array(
		'publishDate' => Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING
	);

	/**
	 * Finds posts by the specified tag
	 *
	 * @param string $tag
	 * @return Tx_Extbase_Persistence_QueryResultInterface The posts
	 */
	public function findByTag($tag) {
		$query = $this->createQuery();

		$query->matching(
			$query->like('tagCloud', '%'. $tag . '%')
		);

		return $query->execute();
	}

	/**
	 * Returns all objects of this repository with matching category
	 *
	 * @param Tx_T3extblog_Domain_Model_Category $category
	 * @return Tx_Extbase_Persistence_QueryResult
	 */
	public function findByCategory(Tx_T3extblog_Domain_Model_Category $category) {
		$query = $this->createQuery();
		$query->matching($query->contains('categories', $category));
		$result = $query->execute();
		return $result;
	}

}
?>