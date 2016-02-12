<?php

namespace TYPO3\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Felix Kopp <felix-source@phorax.com>
 *  (c) 2013-2016 Felix Nagel <info@felixnagel.com>
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
use TYPO3\T3extblog\Exception\InvalidConfigurationException;
use TYPO3\T3extblog\Utility\TypoScriptValidator;

/**
 * BackendBaseController
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
	 * postSubscriberRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\PostSubscriberRepository
	 * @inject
	 */
	protected $postSubscriberRepository;

	/**
	 * blogSubscriberRepository
	 *
	 * @var \TYPO3\T3extblog\Domain\Repository\BlogSubscriberRepository
	 * @inject
	 */
	protected $blogSubscriberRepository;

	/**
	 * The page id
	 *
	 * @var integer
	 */
	protected $pageId;

	/**
	 * Page info
	 *
	 * @var array
	 */
	protected $pageInfo;

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

		} catch (StopActionException $exception) {

			$persistenceManager->persistAll();
			throw $exception;
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
		$this->view->assignMultiple(array(
			'pageId' => $this->pageId,
			'dateFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
			'timeFormat' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm']
		));

		$this->view->assign('pageNotice', $this->pageInfo);
	}

	/**
	 * Initialize actions
	 *
	 * @return void
	 * @throws InvalidConfigurationException
	 */
	public function initializeAction() {
		$this->pageId = intval(GeneralUtility::_GP('id'));
		$this->pageInfo = $this->getBlogRelatedPageInfo();

		try {
			// Validate settings
			TypoScriptValidator::validateSettings($this->settings);

		} catch (InvalidConfigurationException $exception) {

			// On pages with blog records we need to make sure TS is configured so escalate!
			if ($this->pageInfo['show'] === FALSE) {
				throw $exception;
			}
		}
	}

	/**
	 * Check blog related page info
	 *
	 * @return array
	 */
	protected function getBlogRelatedPageInfo() {
		$blogModulePages = $this->getBlogModulePages();
		$blogRelatedPages = $this->getBlogRelatedPages();

		$blogPages = array_unique(array_merge_recursive($blogModulePages, $blogRelatedPages), SORT_REGULAR);
		$blogPagesCurrentPageKey = array_search($this->pageId, array_column($blogPages, 'uid'));

		if ($blogPagesCurrentPageKey !== FALSE) {
			unset($blogPages[$blogPagesCurrentPageKey]);
		}

		return array(
			'show' => ($blogPagesCurrentPageKey === FALSE),
			'pages' => $blogPages
		);
	}

	/**
	 * Get database connection
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabase() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * @return array
	 */
	protected function getBlogModulePages() {
		$table = 'pages ';
		$select = 'uid, title';
		$where = 'module = "t3blog" AND deleted = 0';

		return $this->getDatabase()->exec_SELECTgetRows($select, $table, $where);
	}

	/**
	 * @return array
	 */
	protected function getBlogRelatedPages() {
		$table = 'pages ';
		$table .= 'JOIN tx_t3blog_post as post ';
		$table .= 'JOIN tx_t3blog_com as comment ';
		$table .= 'ON pages.uid = comment.pid OR pages.uid = post.pid ';

		$select = 'pages.title, pages.uid';

		$where = 'pages.deleted = 0 AND ';
		$where .= '(post.deleted = 0 OR comment.deleted = 0)';

		$groupBy = 'pages.uid';

		return $this->getDatabase()->exec_SELECTgetRows($select, $table, $where, $groupBy);
	}

}
