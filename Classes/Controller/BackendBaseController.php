<?php

namespace TYPO3\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Felix Kopp <felix-source@phorax.com>
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;

/**
 * @package t3extblog
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class BackendBaseController extends ActionController {

	/**
	 * objectManager
	 *
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 * @inject
	 */
	protected $objectManager;

	/**
	 * postRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\PostRepository
	 * @inject
	 */
	protected $postRepository;

	/**
	 * postRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\CommentRepository
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
	 * @param RequestInterface $request
	 * @param ResponseInterface $response
	 *
	 * @throws StopActionException
	 * @return void
	 */
	public function processRequest(RequestInterface $request, ResponseInterface $response) {
		/* @var $persistenceManager \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager */
		$persistenceManager = $this->objectManager->get(
			'TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager'
		);

		// We "finally" persist the module data.
		try {
			parent::processRequest($request, $response);
			$persistenceManager->persistAll();
		} catch (StopActionException $e) {
			$persistenceManager->persistAll();
			throw $e;
		}
	}

	/**
	 * Initialize actions
	 *
	 * @throws \RuntimeException
	 * @return void
	 */
	public function initializeAction() {
		$this->pageId = intval(GeneralUtility::_GP('id'));

		// @TODO: Extbase backend modules relies on frontend TypoScript for view, persistence
		// and settings. Thus, we need a TypoScript root template, that then loads the
		// ext_typoscript_setup.txt file of this module. This is nasty, but can not be
		// circumvented until there is a better solution in extbase.
		// For now we throw an exception if no settings are detected.
		if (empty($this->settings)) {
			throw new \RuntimeException(
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
	 * @param ViewInterface $view The view to be initialized
	 *
	 * @return void
	 */
	protected function initializeView(ViewInterface $view) {
		$moduleName = GeneralUtility::_GET('M');
		$moduleToken = FormProtectionFactory::get()->generateToken('moduleCall', $moduleName);

		$this->view->assignMultiple(array(
			'pageId' => $this->pageId,
			'returnUrl' => urlencode('mod.php?M=' . $moduleName . '&id=' . $this->pageId . '&moduleToken=' . $moduleToken),
			'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
			'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm']
		));
	}

}
