<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Felix Kopp <felix-source@phorax.com>
 *  (c) 2013-2014 Felix Nagel <info@felixnagel.com>
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
class Tx_T3extblog_Controller_BackendBaseController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * objectManager
	 *
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * postRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_PostRepository
	 * @inject
	 */
	protected $postRepository;

	/**
	 * postRepository
	 *
	 * @var Tx_T3extblog_Domain_Repository_CommentRepository
	 * @inject
	 */
	protected $commentRepository;

	/**
	 * The page id
	 *
	 * @var integer
	 */
	protected $pageId;

	/**
	 * Load and persist module data
	 *
	 * @param Tx_Extbase_MVC_RequestInterface  $request
	 * @param Tx_Extbase_MVC_ResponseInterface $response
	 *
	 * @throws Tx_Extbase_MVC_Exception_StopAction
	 * @return void
	 */
	public function processRequest(Tx_Extbase_MVC_RequestInterface $request, Tx_Extbase_MVC_ResponseInterface $response) {
		/* @var $persistenceManager Tx_Extbase_Persistence_Manager */
		$persistenceManager = $this->objectManager->get('Tx_Extbase_Persistence_Manager');

		// We "finally" persist the module data.
		try {
			parent::processRequest($request, $response);
			$persistenceManager->persistAll();
		} catch (Tx_Extbase_MVC_Exception_StopAction $e) {
			$persistenceManager->persistAll();
			throw $e;
		}
	}

	/**
	 * Initialize actions
	 *
	 * @throws RuntimeException
	 * @return void
	 */
	public function initializeAction() {
		$this->pageId = intval(t3lib_div::_GP('id'));

		// @TODO: Extbase backend modules relies on frontend TypoScript for view, persistence
		// and settings. Thus, we need a TypoScript root template, that then loads the
		// ext_typoscript_setup.txt file of this module. This is nasty, but can not be
		// circumvented until there is a better solution in extbase.
		// For now we throw an exception if no settings are detected.
		if (empty($this->settings)) {
			throw new RuntimeException(
				'No settings detected. This module can not work then. ' .
				'This usually happens if there is no frontend TypoScript template with root flag set. ' .
				'Please create a frontend page with a TypoScript root template.',
				1344375003
			);
		}
	}

	/**
	 * Initializes the view before invoking an action method.
	 *
	 * @param Tx_Extbase_MVC_View_ViewInterface $view The view to be initialized
	 *
	 * @return void
	 */
	protected function initializeView(Tx_Extbase_MVC_View_ViewInterface $view) {
		$this->view->assignMultiple(array(
			'pageId' => $this->pageId,
			'returnUrl' => urlencode('mod.php?M=web_T3extblogTxT3extblog&id=' . $this->pageId),
			'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
			'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm']
		));
	}

}

?>