<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend\Uri;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Exception\InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Fluid\ViewHelpers\Uri\ActionViewHelper as CoreActionViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * A view helper for creating URIs to extbase actions.
 *
 * This a modified version of the default Extbase class forcing FE links within BE context.
 */
class ActionViewHelper extends CoreActionViewHelper
{
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        if (($GLOBALS['TYPO3_REQUEST'] ?? null) instanceof ServerRequestInterface
            && ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()
        ) {
            return parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);
        }

        return self::renderStaticFrontend($arguments, $renderChildrenClosure, $renderingContext);
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
    protected static function renderStaticFrontend(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        if ($arguments['pageUid'] === null || !(int) $arguments['pageUid']) {
            throw new InvalidArgumentException('Missing pageUid argument for extbase link generation from BE context. Check your template!');
        }

        if ($arguments['controller'] === null || $arguments['extensionName'] === null || $arguments['pluginName'] === null) {
            throw new InvalidArgumentException('Missing arguments for extbase link generation from BE context. Check your template!');
        }

        // @todo Remove this when TYPO3 11 is no longer supported
        if (isset($arguments['addQueryStringMethod'])) {
            trigger_error('Using the argument "addQueryStringMethod" in <f:uri.action> ViewHelper has no effect anymore and will be removed in TYPO3 v12. Remove the argument in your fluid template, as it will result in a fatal error.', E_USER_DEPRECATED);
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
        /** @var bool $addQueryString */
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
        $uriBuilder = $renderingContext->getUriBuilder();
        $uriBuilder->reset();

        if ($pageUid > 0) {
            $uriBuilder->setTargetPageUid($pageUid);
        }

        if ($pageType > 0) {
            $uriBuilder->setTargetPageType($pageType);
        }

        if ($noCache === true) {
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

        if ($absolute === true) {
            $uriBuilder->setCreateAbsoluteUri($absolute);
        }

        if ($addQueryString === true) {
            $uriBuilder->setAddQueryString($addQueryString);
        }

        if (is_array($argumentsToBeExcludedFromQueryString)) {
            $uriBuilder->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString);
        }

        if ($linkAccessRestrictedPages === true) {
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

        $controllerArguments = $controllerArguments ?? [];

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
