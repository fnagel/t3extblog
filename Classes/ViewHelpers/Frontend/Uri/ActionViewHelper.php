<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend\Uri;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Exception\Exception;
use FelixNagel\T3extblog\Exception\InvalidArgumentException;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * A view helper for creating URIs to extbase actions.
 *
 * This a modified version of the default Extbase class forcing FE links within BE context.
 */
class ActionViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments(): void
    {
        $this->registerArgument('action', 'string', 'Target action');
        $this->registerArgument('arguments', 'array', 'Arguments', false, []);
        $this->registerArgument('controller', 'string', 'Target controller. If NULL current controllerName is used');
        $this->registerArgument('extensionName', 'string', 'Target Extension Name (without `tx_` prefix and no underscores). If NULL the current extension name is used');
        $this->registerArgument('pluginName', 'string', 'Target plugin. If empty, the current plugin name is used');
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination');
        $this->registerArgument('pageType', 'int', 'Type of the target page. See typolink.parameter', false, 0);
        $this->registerArgument('noCache', 'bool', 'Set this to disable caching for the target page. You should not need this.', false);
        $this->registerArgument('section', 'string', 'The anchor to be added to the URI', false, '');
        $this->registerArgument('format', 'string', 'The requested format, e.g. ".html', false, '');
        $this->registerArgument('linkAccessRestrictedPages', 'bool', 'If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.', false, false);
        $this->registerArgument('additionalParams', 'array', 'additional query parameters that won\'t be prefixed like $arguments (overrule $arguments)', false, []);
        $this->registerArgument('absolute', 'bool', 'If set, an absolute URI is rendered', false, false);
        $this->registerArgument('addQueryString', 'string', 'If set, the current query parameters will be kept in the URL. If set to "untrusted", then ALL query parameters will be added. Be aware, that this might lead to problems when the generated link is cached.', false, false);
        $this->registerArgument('argumentsToBeExcludedFromQueryString', 'array', 'arguments to be removed from the URI. Only active if $addQueryString = TRUE', false, []);
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext): string
    {
        /** @var RenderingContext $renderingContext */
        $request = $renderingContext->getRequest();
        if (!$request instanceof RequestInterface) {
            throw new Exception(
                'ViewHelper t3b:uri.action can be used only in extbase context and needs a request implementing extbase RequestInterface.',
                1639819692
            );
        }

        return self::renderStaticFrontend($arguments, $renderingContext);
    }

    /**
     * Always renders a FE link but with limited functionality.
     *
     * Some more arguments are required due to routing limitations.
     *
     * @SuppressWarnings("PHPMD.CyclomaticComplexity")
     * @SuppressWarnings("PHPMD.NPathComplexity")
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @see \TYPO3\CMS\Fluid\ViewHelpers\Uri\ActionViewHelper::renderStatic
     * @return string
     */
    protected static function renderStaticFrontend(array $arguments, RenderingContext $renderingContext)
    {
        /** @var RequestInterface $request */
        $request = $renderingContext->getRequest();

        if ($arguments['pageUid'] === null || !(int) $arguments['pageUid']) {
            throw new InvalidArgumentException('Missing pageUid argument for extbase link generation from BE context. Check your template!');
        }

        if ($arguments['controller'] === null || $arguments['extensionName'] === null || $arguments['pluginName'] === null) {
            throw new InvalidArgumentException('Missing arguments for extbase link generation from BE context. Check your template!');
        }

        /** @var int $pageUid */
        $pageUid = $arguments['pageUid'] ?? 0;
        /** @var int $pageType */
        $pageType = $arguments['pageType'] ?? 0;
        /** @var bool $noCache */
        $noCache = $arguments['noCache'] ?? false;
        /** @var string|null $section */
        $section = $arguments['section'] ?? null;
        /** @var string|null $format */
        $format = $arguments['format'] ?? null;
        /** @var bool $linkAccessRestrictedPages */
        $linkAccessRestrictedPages = $arguments['linkAccessRestrictedPages'] ?? false;
        /** @var array|null $additionalParams */
        $additionalParams = $arguments['additionalParams'] ?? null;
        /** @var bool $absolute */
        $absolute = $arguments['absolute'] ?? false;
        /** @var bool|string $addQueryString */
        $addQueryString = $arguments['addQueryString'] ?? false;
        /** @var array|null $argumentsToBeExcludedFromQueryString */
        $argumentsToBeExcludedFromQueryString = $arguments['argumentsToBeExcludedFromQueryString'] ?? null;
        /** @var string|null $action */
        $action = $arguments['action'] ?? null;
        /** @var string|null $controller */
        $controller = $arguments['controller'] ?? null;
        /** @var string|null $extensionName */
        $extensionName = $arguments['extensionName'] ?? null;
        /** @var string|null $pluginName */
        $pluginName = $arguments['pluginName'] ?? null;
        /** @var array|null $arguments */
        $arguments = self::uriFor($action, $arguments['arguments'], $controller, $extensionName, $pluginName);

        /** @var UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $uriBuilder->reset()->setRequest($request);

        if ($pageUid > 0) {
            $uriBuilder->setTargetPageUid($pageUid);
        }

        if ($pageType > 0) {
            $uriBuilder->setTargetPageType($pageType);
        }

        if ($noCache) {
            $uriBuilder->setNoCache($noCache);
        }

        if (is_string($section)) {
            $uriBuilder->setSection($section);
        }

        if (is_string($format)) {
            $uriBuilder->setFormat($format);
        }

        if (is_array($additionalParams)) {
            ArrayUtility::mergeRecursiveWithOverrule($arguments, $additionalParams);
        }

        if ($absolute) {
            $uriBuilder->setCreateAbsoluteUri($absolute);
        }

        if ($addQueryString && $addQueryString !== 'false') {
            $uriBuilder->setAddQueryString($addQueryString);
        }

        if (is_array($argumentsToBeExcludedFromQueryString)) {
            $uriBuilder->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString);
        }

        if ($linkAccessRestrictedPages) {
            $uriBuilder->setLinkAccessRestrictedPages($linkAccessRestrictedPages);
        }

        $uriBuilder->setArguments($arguments);

        return $uriBuilder->buildFrontendUri();
    }

    /** Simplified version of UriBuilder::uriFor
     *
     * @see \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder::uriFor
     */
    protected static function uriFor(
        ?string $actionName = null,
        ?array $controllerArguments = null,
        ?string $controllerName = null,
        ?string $extensionName = null,
        ?string $pluginName = null
    ): array {
        /* @var $extensionService ExtensionService */
        $extensionService = GeneralUtility::makeInstance(ExtensionService::class);

        $controllerArguments ??= [];

        if ($actionName !== null) {
            $controllerArguments['action'] = $actionName;
        }

        if ($controllerName !== null) {
            $controllerArguments['controller'] = $controllerName;
        }

        if ($pluginName === null) {
            $pluginName = $extensionService->getPluginNameByAction(
                $extensionName,
                $controllerArguments['controller'],
                $controllerArguments['action'] ?? null
            );
        }

        $pluginNamespace = $extensionService->getPluginNamespace($extensionName, $pluginName);

        return [$pluginNamespace => $controllerArguments];
    }
}
