<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Repository\BlogSubscriberRepository;
use FelixNagel\T3extblog\Domain\Repository\CommentRepository;
use FelixNagel\T3extblog\Domain\Repository\PostRepository;
use FelixNagel\T3extblog\Domain\Repository\PostSubscriberRepository;
use FelixNagel\T3extblog\Traits\LoggingTrait;
use TYPO3\CMS\Core\Database\ConnectionPool;
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
    use LoggingTrait;

    /**
     * postRepository.
     *
     * @var PostRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $postRepository;

    /**
     * commentRepository.
     *
     * @var CommentRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $commentRepository;

    /**
     * postSubscriberRepository.
     *
     * @var PostSubscriberRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $postSubscriberRepository;

    /**
     * blogSubscriberRepository.
     *
     * @var BlogSubscriberRepository
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
     * The database connection.
     *
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * BackendBaseController constructor.
     *
     * @param PostRepository $postRepository
     * @param CommentRepository $commentRepository
     * @param PostSubscriberRepository $postSubscriberRepository
     * @param BlogSubscriberRepository $blogSubscriberRepository
     */
    public function __construct(
        PostRepository $postRepository,
        CommentRepository $commentRepository,
        PostSubscriberRepository $postSubscriberRepository,
        BlogSubscriberRepository $blogSubscriberRepository
    ) {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->postSubscriberRepository = $postSubscriberRepository;
        $this->blogSubscriberRepository = $blogSubscriberRepository;
    }

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
                $this->getLog()->exception($exception, [
                    'pid' => $this->pageId,
                    'context' => 'backend',
                ]);
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
     * @return ConnectionPool
     */
    protected function getDatabaseConnection()
    {
        if ($this->connectionPool === null) {
            $this->connectionPool =  GeneralUtility::makeInstance(ConnectionPool::class);
        }

        return $this->connectionPool;
    }

    /**
     * @return array
     */
    protected function getBlogRelatedPages()
    {
        $pages = array_merge_recursive(
            // Get pages with set module property
            $this->getBlogModulePages(),
            // Split the join queries because otherwise the query is awful slow
            $this->getPagesWithBlogRecords(['tx_t3blog_post', 'tx_t3blog_com']),
            $this->getPagesWithBlogRecords(['tx_t3blog_com_nl', 'tx_t3blog_blog_nl'])
        );

        return array_unique($pages, SORT_REGULAR);
    }

    /**
     * Run query for getting page info.
     *
     * @return array
     */
    protected function getBlogModulePages()
    {
        $table = 'pages';
        $queryBuilder = $this->getDatabaseConnection()->getQueryBuilderForTable($table);
        $queryBuilder
            ->select('uid', 'title')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('module', $queryBuilder->createNamedParameter('t3blog')),
                $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
            );

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * @param $joinTables
     *
     * @return array
     */
    protected function getPagesWithBlogRecords($joinTables)
    {
        $table = 'pages';
        $queryBuilder = $this->getDatabaseConnection()->getQueryBuilderForTable($table);
        $queryBuilder
            ->select($table . '.uid', $table . '.title')
            ->from($table)
            ->groupBy($table.'.uid');

        foreach ($joinTables as $joinTable) {
            $queryBuilder->leftJoin(
                $table,
                $joinTable,
                $joinTable,
                $queryBuilder->expr()->eq(
                    $table . '.uid',
                    $queryBuilder->quoteIdentifier($joinTable . '.pid')
                )
            );
        }

        return $queryBuilder->execute()->fetchAll();
    }
}
