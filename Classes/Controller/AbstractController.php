<?php

namespace FelixNagel\T3extblog\Controller;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity as Message;
use TYPO3\CMS\Core\View\ViewInterface;
use FelixNagel\T3extblog\Utility\FrontendUtility;
use FelixNagel\T3extblog\Service\FlushCacheService;
use FelixNagel\T3extblog\Service\RateLimiterServiceInterface;
use FelixNagel\T3extblog\Traits\LoggingTrait;
use FelixNagel\T3extblog\Utility\TypoScript;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use FelixNagel\T3extblog\Domain\Model\AbstractEntity;
use FelixNagel\T3extblog\Domain\Model\Category;
use FelixNagel\T3extblog\Domain\Model\Comment;
use FelixNagel\T3extblog\Domain\Model\Post;
use FelixNagel\T3extblog\Utility\TypoScriptValidator;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Core\Cache\CacheTag;

/**
 * Abstract base controller.
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
abstract class AbstractController extends ActionController
{
    use LoggingTrait;

    protected ViewInterface $view;

    protected ?RateLimiterServiceInterface $rateLimiter = null;

    /**
     * Injects the Configuration Manager and is initializing the framework settings
     * Function is used to override the merge of settings via TS & flexforms
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        parent::injectConfigurationManager($configurationManager);

        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            't3extblog',
            't3extblog_blogsystem'
        );

        if (isset($settings['settings']['overrideFlexformSettingsIfEmpty'])) {
            /** @var TypoScript $typoScriptUtility */
            $typoScriptUtility = GeneralUtility::makeInstance(TypoScript::class);
            $this->settings = $typoScriptUtility->override($this->settings, $settings);
        }
    }

    public function processRequest(RequestInterface $request): ResponseInterface
    {
        $response = null;

        try {
            $response = parent::processRequest($request);
        } catch (\Exception $exception) {
            $this->handleKnownExceptionsElseThrowAgain($exception);
        }

        return $response;
    }

    protected function handleKnownExceptionsElseThrowAgain(\Throwable $exception): never
    {
        throw $exception;
    }

    protected function initializeAction(): void
    {
        try {
            $this->validateTypoScriptConfiguration();
        } catch (\Exception $exception) {
            $this->getLog()->exception($exception, [
                'pid' => FrontendUtility::getPageUid(),
                'context' => 'frontend',
            ]);
            throw $exception;
        }

        $this->addDefaultCacheTags();
    }

    /**
     * Validate TypoScript settings.
     */
    protected function validateTypoScriptConfiguration()
    {
        TypoScriptValidator::validateSettings($this->settings);

        $frameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            $this->request->getControllerExtensionName(),
            $this->request->getPluginName()
        );
        TypoScriptValidator::validateFrameworkConfiguration($frameworkConfiguration);
    }

    /**
     * Override getErrorFlashMessage to present nice flash error messages.
     */
    protected function getErrorFlashMessage(): bool|string
    {
        $defaultFlashMessage = parent::getErrorFlashMessage();
        $locallangKey = sprintf('error.%s.%s', lcfirst($this->request->getControllerName()), $this->actionMethodName);

        return $this->translate($locallangKey, $defaultFlashMessage);
    }

    /**
     * Helper function to render localized flashmessages.
     */
    protected function addFlashMessageByKey(string $key, Message $severity = Message::OK)
    {
        $messageLocallangKey = sprintf('flashMessage.%s.%s', lcfirst($this->request->getControllerName()), $key);
        $localizedMessage = $this->translate($messageLocallangKey, '['.$messageLocallangKey.']');

        $titleLocallangKey = sprintf('%s.title', $messageLocallangKey);
        $localizedTitle = $this->translate($titleLocallangKey, '['.$titleLocallangKey.']');

        $this->addFlashMessage($localizedMessage, $localizedTitle, $severity);
    }

    /**
     * Helper function to check if flashmessages have been saved until now.
     */
    protected function hasFlashMessages(): bool
    {
        $messages = $this->getFlashMessageQueue()->getAllMessages();

        return count($messages) > 0;
    }

    /**
     * Clear cache of current page.
     */
    protected function clearPageCache()
    {
        FlushCacheService::clearPageCache();
    }

    protected function pageNotFoundAndExit(string $message = 'Entity not found.'): never
    {
        $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction(
            $this->request,
            $message
        );

        throw new PropagateResponseException($response, 1576748646);
    }

    /**
     * Persist all records to database.
     */
    protected function persistAllEntities(): void
    {
        /* @var $persistenceManager PersistenceManager */
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $persistenceManager->persistAll();
    }

    /**
     * Helper function to use localized strings in BlogExample controllers.
     *
     * @param string $key            locallang key
     * @param string $defaultMessage the default message to show if key was not found
     */
    protected function translate(string $key, string $defaultMessage = ''): string
    {
        $message = LocalizationUtility::translate($key, 'T3extblog');

        if ($message === null) {
            $message = $defaultMessage;
        }

        return $message;
    }

    /**
     * Add page cache tag by string or object.
     *
     * @param mixed $object A cache tag string or a blog model object
     */
    protected function addCacheTags(mixed $object = null): void
    {
        $tags = is_array($object) ? $object : [];

        if (is_string($object)) {
            $tags[] = $object;
        }

        // Add base PID based tag
        if ($object instanceof AbstractEntity) {
            $tags[] = 'tx_t3extblog_'.$object->getPid();
        }

        // Add model based tag
        if ($object instanceof Post) {
            $tags[] = 'tx_t3blog_post_pid_'.$object->getPid();
        }

        if ($object instanceof Comment) {
            $tags[] = 'tx_t3blog_com_pid_'.$object->getPid();
        }

        if ($object instanceof Category) {
            $tags[] = 'tx_t3blog_cat_pid_'.$object->getPid();
        }

        // @extensionScannerIgnoreLine
        $this->request->getAttribute('frontend.cache.collector')->addCacheTags(
            ...array_map(fn (string $tag) => new CacheTag($tag), $tags)
        );
    }

    /**
     * Add basic cache tag for all related pages.
     */
    protected function addDefaultCacheTags()
    {
        static $cacheTagsSet = false;

        if ($cacheTagsSet === false) {
            // @extensionScannerIgnoreLine
            $this->addCacheTags(['tx_t3extblog']);

            // We only want to set the tag once in one request
            $cacheTagsSet = true;
        }
    }

    protected function assignPaginationVariables(QueryResultInterface $result, array $paginationConfig, int $page = 1): void
    {
        $paginator = new QueryResultPaginator($result, $page, $paginationConfig['itemsPerPage'] ?: 10);

        $this->view->assignMultiple([
            'paginator' => $paginator,
            'pagination' => new SimplePagination($paginator),
            'totalItems' => $result->count(),
        ]);
    }

    protected function paginationHtmlResponse(QueryResultInterface $result, array $paginationConfig, int $page = 1): ResponseInterface
    {
        $this->assignPaginationVariables($result, $paginationConfig, $page);

        return $this->htmlResponse();
    }

    protected function paginationXmlResponse(QueryResultInterface $result, array $paginationConfig, int $page = 1): ResponseInterface
    {
        $this->assignPaginationVariables($result, $paginationConfig, $page);

        return $this->xmlResponse();
    }

    protected function xmlResponse(?string $xml = null): ResponseInterface
    {
        $this->view->getTemplatePaths()->setFormat('xml');

        return $this->responseFactory->createResponse()
            ->withHeader('Content-Type', 'text/xml; charset=utf-8')
            ->withBody($this->streamFactory->createStream((string)($xml ?? $this->view->render())));
    }

    protected function initRateLimiter(string $key, array $settings): RateLimiterServiceInterface
    {
        return $this->getRateLimiter()->create($this->request, $key, $settings)->consume($key);
    }

    protected function getRateLimiter(): RateLimiterServiceInterface
    {
        if ($this->rateLimiter === null) {
            $this->rateLimiter = GeneralUtility::makeInstance(RateLimiterServiceInterface::class);
        }

        return $this->rateLimiter;
    }
}
