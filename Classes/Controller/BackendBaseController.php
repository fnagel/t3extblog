<?php

namespace FelixNagel\T3extblog\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Felix Kopp <felix-source@phorax.com>
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
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
use FelixNagel\T3extblog\Exception\InvalidConfigurationException;
use FelixNagel\T3extblog\Utility\TypoScriptValidator;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * BackendBaseController.
 */
class BackendBaseController extends ActionController
{
    /**
     * objectManager.
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $objectManager;

    /**
     * postRepository.
     *
     * @var \FelixNagel\T3extblog\Domain\Repository\PostRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $postRepository;

    /**
     * postRepository.
     *
     * @var \FelixNagel\T3extblog\Domain\Repository\CommentRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $commentRepository;

    /**
     * postSubscriberRepository.
     *
     * @var \FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $postSubscriberRepository;

    /**
     * blogSubscriberRepository.
     *
     * @var \FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $blogSubscriberRepository;

    /**
     * The page id.
     *
     * @var int
     */
    protected $pageId;

    /**
     * Page info.
     *
     * @var array
     */
    protected $pageInfo;

    /**
     * Load and persist module data.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @throws StopActionException
     */
    public function processRequest(RequestInterface $request, ResponseInterface $response)
    {
        /* @var $persistenceManager PersistenceManager */
        $persistenceManager = $this->objectManager->get(PersistenceManager::class);

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
     */
    protected function initializeView(ViewInterface $view)
    {
        $dateTimeFormat = trim($this->settings['backend']['dateTimeFormat']);
        if (empty($dateTimeFormat)) {
            $dateTimeFormat = $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] . ' ' .
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'];
        }

        $this->view->assignMultiple([
            'pageId' => $this->pageId,
            'dateTimeFormat' => $dateTimeFormat,
            'pageNotice' => $this->pageInfo,
        ]);
    }

    /**
     * Initialize actions.
     *
     * @throws InvalidConfigurationException
     */
    public function initializeAction()
    {
        $this->pageId = intval(GeneralUtility::_GP('id'));
        $this->pageInfo = $this->getBlogRelatedPageInfo();

        try {
            // Validate settings
            TypoScriptValidator::validateSettings($this->settings);
        } catch (InvalidConfigurationException $exception) {
            // On pages with blog records we need to make sure TS is configured so escalate!
            if ($this->pageInfo['show'] === false) {
                throw $exception;
            }
        }
    }

    /**
     * Check blog related page info.
     *
     * @return array
     */
    protected function getBlogRelatedPageInfo()
    {
        $blogPages = $this->getBlogRelatedPages();
        $blogPagesCurrentPageKey = array_search($this->pageId, array_column($blogPages, 'uid'));

        if ($blogPagesCurrentPageKey !== false) {
            unset($blogPages[$blogPagesCurrentPageKey]);
        }

        return [
            'show' => ($blogPagesCurrentPageKey === false),
            'pages' => $blogPages,
        ];
    }

    /**
     * Get database connection.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabase()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @return array
     */
    protected function getBlogRelatedPages()
    {
        $pages = array_merge_recursive(
            // Get pages with set module property
            $this->execBlogPageRelatedQuery('pages', 'pages.module = "t3blog"'),
            // Split the join queries because otherwise the query is awful slow
            $this->getPagesWithBlogRecords(['tx_t3blog_post', 'tx_t3blog_com']),
            $this->getPagesWithBlogRecords(['tx_t3blog_com_nl', 'tx_t3blog_blog_nl'])
        );

        return array_unique($pages, SORT_REGULAR);
    }

    /**
     * Run query for getting page info.
     *
     * @param string $table           Needs to be 'pages' or at least related (think of joins)
     * @param string $additionalWhere
     * @param string $groupBy
     *
     * @return array
     */
    protected function execBlogPageRelatedQuery($table, $additionalWhere = '', $groupBy = '')
    {
        $select = 'pages.uid, pages.title';
        $where = 'pages.deleted = 0 AND '.$additionalWhere;

        return $this->getDatabase()->exec_SELECTgetRows($select, $table, $where, $groupBy);
    }

    /**
     * @param $joinTables
     *
     * @return array
     */
    protected function getPagesWithBlogRecords($joinTables)
    {
        $table = 'pages';
        $whereArray = [];

        foreach ($joinTables as $joinTable) {
            $table .= ' LEFT JOIN '.$joinTable.' ON pages.uid = '.$joinTable.'.pid';
            $whereArray[] = $joinTable.'.deleted = 0';
        }

        return $this->execBlogPageRelatedQuery($table, '('.implode(' OR ', $whereArray).')', 'pages.uid');
    }
}
