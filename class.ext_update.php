<?php

/**
 * Class ext_update
 *
 * Performs update tasks for extension t3extblog
 */
class ext_update {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	public function main() {
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_t3blog_com', 'mails_sent IS NULL', array('mails_sent' => 1));
		return $GLOBALS['TYPO3_DB']->sql_affected_rows() . ' rows have been updated';
	}

	/**
	 * Checks how many rows are found and returns true if there are any
	 * (this function is called from the extension manager)
	 *
	 * @param	string		$what: what should be updated
	 * @return	boolean
	 */
	public function access($what = 'all') {
		return $GLOBALS['BE_USER']->isAdmin();
	}

}
