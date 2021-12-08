<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend\Uri;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Fluid\ViewHelpers\Uri\ActionViewHelper as CoreActionViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * A view helper for creating URIs to extbase actions.
 *
 * This a modified version of the default Extbase class which enables us to
 * use a FE link within a BE context.
 */
class ActionViewHelper extends CoreActionViewHelper
{
    use CompileWithRenderStatic;

    /**
     * @inheritDoc
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
            return parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);
        }

        return self::renderFrontendLink($renderingContext->getControllerContext()->getUriBuilder(), $arguments);
    }

    /**
     * @param array  $arguments Arguments
     * @return string Rendered link
     */
    protected static function renderFrontendLink(UriBuilder $uriBuilder, array $arguments): string
    {
        if ($arguments['pageUid'] === null || !(int) $arguments['pageUid']) {
            throw new \Exception('Missing pageUid argument for extbase link generation from BE context. Check your template!');
        }

        if ($arguments['controller'] === null || $arguments['extensionName'] === null || $arguments['pluginName'] === null) {
            throw new \Exception('Missing arguments for extbase link generation from BE context. Check your template!');
        }

        $uri = $uriBuilder->reset()
            ->setTargetPageUid($arguments['pageUid'])
            ->setTargetPageType($arguments['pageType'])
            ->setNoCache((bool) $arguments['noCache'])
            ->setSection($arguments['section'])
            ->setFormat($arguments['format'])
            ->setLinkAccessRestrictedPages((bool) $arguments['linkAccessRestrictedPages'])
            ->setArguments(self::uriFor(
                $arguments['action'],
                $arguments['arguments'],
                $arguments['controller'],
                $arguments['extensionName'],
                $arguments['pluginName'],
                $arguments['format'],
                $arguments['additionalParams']
            ))
            ->setCreateAbsoluteUri((bool) $arguments['absolute'])
            ->setAddQueryString((bool) $arguments['addQueryString'])
            ->setArgumentsToBeExcludedFromQueryString($arguments['argumentsToBeExcludedFromQueryString'])
            ->buildFrontendUri();

        return $uri;
    }

    /**
     * Creates an URI used for linking to an Extbase action.
     * Works in Frontend and Backend mode of TYPO3.
     *
     * @param string $actionName Name of the action to be called
     * @param array  $controllerArguments Additional query parameters. Will be "namespaced" and merged with $arguments.
     * @param string $controllerName Name of the target controller. If not set, current ControllerName is used.
     * @param string $extensionName Name of the target extension, without underscores. If not set, current ExtensionName is used.
     * @param string $pluginName Name of the target plugin. If not set, current PluginName is used.
     * @param string $format The requested format, e.g. ".html
     * @param array  $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
     *
     */
    protected static function uriFor(
        string $actionName = null,
        array $controllerArguments = [],
        string $controllerName = null,
        string $extensionName = null,
        string $pluginName = null,
        string $format = '',
        array $additionalParams = []
    ): array {
        /* @var $extensionService ExtensionService */
        $extensionService = GeneralUtility::makeInstance(ExtensionService::class);

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
                $controllerArguments['action']
            );
        }
        
        if ($format !== '') {
            $controllerArguments['format'] = $format;
        }

        $pluginNamespace = $extensionService->getPluginNamespace($extensionName, $pluginName);
        $prefixedControllerArguments = [$pluginNamespace => $controllerArguments];

        return array_merge_recursive($additionalParams, $prefixedControllerArguments);
    }
}
