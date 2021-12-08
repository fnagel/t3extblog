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
use FelixNagel\T3extblog\Service\BackendModuleService;
use FelixNagel\T3extblog\Traits\LoggingTrait;
use FelixNagel\T3extblog\Utility\BlogPageSearchUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use FelixNagel\T3extblog\Exception\InvalidConfigurationException;
use FelixNagel\T3extblog\Utility\TypoScriptValidator;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * AbstractBackendController.
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
abstract class AbstractBackendController extends ActionController
{
    use LoggingTrait;

    /**
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * postRepository.
     */
    protected PostRepository $postRepository;

    /**
     * commentRepository.
     */
    protected CommentRepository $commentRepository;

    /**
     * postSubscriberRepository.
     */
    protected PostSubscriberRepository $postSubscriberRepository;

    /**
     * blogSubscriberRepository.
     */
    protected BlogSubscriberRepository $blogSubscriberRepository;

    /**
     * The page id.
     */
    protected ?int $pageId = null;

    /**
     * Page info.
     */
    protected array $pageInfo = [];

    /**
     * The database connection.
     */
    protected ConnectionPool $connectionPool;

    /**
     * BackendBaseController constructor.
     *
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
     * @inheritdoc
     */
    public function processRequest(RequestInterface $request): ResponseInterface
    {
        /* @var $persistenceManager PersistenceManager */
        $persistenceManager = $this->objectManager->get(PersistenceManager::class);

        // We "finally" persist the module data.
        try {
            parent::processRequest($request);
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
        /** @var BackendTemplateView $view */
        parent::initializeView($view);

        $dateTimeFormat = trim($this->settings['backend']['dateTimeFormat']);
        if (empty($dateTimeFormat)) {
            $dateTimeFormat = $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] . ' ' .
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'];
        }

        // Configure module header
        $moduleService = $this->objectManager->get(
            BackendModuleService::class,
            $this->objectManager,
            $this->view,
            $this->pageId
        );
        $moduleService->addMetaInformation();
        $moduleService->addViewAssets(
            ['TYPO3/CMS/Backend/ContextMenu'],
            ['EXT:t3extblog/Resources/Public/Css/Backend/Style.css']
        );
        $moduleService->addViewHeaderMenu(
            $this->request,
            $this->getViewHeaderMenuItems(),
            'T3ExtblogModuleMenu'
        );
        $moduleService->addViewHeaderButtons(
            $this->getViewHeaderButtonItems(),
            'web_T3extblogTxT3extblog'
        );

        $this->view->assignMultiple([
            'pageId' => $this->pageId,
            'dateTimeFormat' => $dateTimeFormat,
            'pageNotice' => $this->pageInfo,
        ]);
    }

    
    protected function getViewHeaderMenuItems(): array
    {
        return [
            'backendDashboardIndex' => [
                'controller' => 'BackendDashboard',
                'action' => 'index',
                'label' => $this->translate('module.dashboard.title'),
            ],
            'backendPostIndex' => [
                'controller' => 'BackendPost',
                'action' => 'index',
                'label' => $this->translate('module.post.title'),
            ],

            // Comment
            'backendCommentIndex' => [
                'controller' => 'BackendComment',
                'action' => 'index',
                'label' => $this->translate('module.comment.group') . ': ' .
                    $this->translate('module.comment.title.all'),
            ],
            'backendCommentListPending' => [
                'controller' => 'BackendComment',
                'action' => 'listPending',
                'label' => $this->translate('module.comment.group') . ': ' .
                    $this->translate('module.comment.title.pending'),
            ],

            // Subscriber
            'backendSubscriberIndexPostSubscriber' => [
                'controller' => 'BackendSubscriber',
                'action' => 'indexPostSubscriber',
                'label' => $this->translate('module.subscriber.group') . ': ' .
                    $this->translate('module.subscriber.post.title'),
            ],
            'backendSubscriberIndexBlogSubscriber' => [
                'controller' => 'BackendSubscriber',
                'action' => 'indexBlogSubscriber',
                'label' => $this->translate('module.subscriber.group') . ': ' .
                    $this->translate('module.subscriber.blog.title'),
            ],
        ];
    }

    
    protected function getViewHeaderButtonItems(): array
    {
        return [
            'post' => [
                'table' => 'tx_t3blog_post',
                'label' => $this->translate('module.buttons.new.post'),
                'icon' => 'extensions-t3extblog-post'
            ],
            'comment' => [
                'table' => 'tx_t3blog_com',
                'label' => $this->translate('module.buttons.new.comment'),
                'icon' => 'extensions-t3extblog-comment'
            ],
            'category' => [
                'table' => 'tx_t3blog_cat',
                'label' => $this->translate('module.buttons.new.category'),
                'icon' => 'extensions-t3extblog-category'
            ],
        ];
    }

    /**
     * Initialize actions.
     *
     * @throws InvalidConfigurationException
     */
    protected function initializeAction()
    {
        $this->pageId = (int) GeneralUtility::_GP('id');
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
     */
    protected function getBlogRelatedPageInfo(): array
    {
        $blogPages = BlogPageSearchUtility::getBlogRelatedPages();
        $blogPagesCurrentPageKey = array_search($this->pageId, array_column($blogPages, 'uid'));

        if ($blogPagesCurrentPageKey !== false) {
            unset($blogPages[$blogPagesCurrentPageKey]);
        }

        return [
            'show' => ($blogPagesCurrentPageKey === false),
            'pages' => $blogPages,
        ];
    }

    
    protected function translate(string $key): string
    {
        return $this->getLanguageService()->sL('LLL:EXT:t3extblog/Resources/Private/Language/locallang.xlf:' . $key);
    }

    
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
