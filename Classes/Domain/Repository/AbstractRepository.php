<?php

namespace TYPO3\T3extblog\Domain\Repository;

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

use TYPO3\T3extblog\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * AbstractRepository
 */
class AbstractRepository extends Repository {

	/**
	 * @param null $pageUid
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryInterface
	 */
	public function createQuery($pageUid = NULL) {
		$query = parent::createQuery();

		if ($pageUid !== NULL) {
			$query->getQuerySettings()->setStoragePageIds(array($pageUid));
		}

		return $query;
	}

	/**
	 * Enable fields for BE and FE
	 *
	 * @param string $table
	 *
	 * @return string
	 */
	public function enableFields($table) {
		$where = '';

		if (TYPO3_MODE === 'FE') {
			$where .= GeneralUtility::getTsFe()->sys_page->enableFields($table);
		} else {
			$where .= BackendUtility::BEenableFields($table);
			$where .= BackendUtility::deleteClause($table);
		}

		return $where;
	}
}

