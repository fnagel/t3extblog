<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend\Uri;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2019 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Fluid\ViewHelpers\Uri\ActionViewHelper as CoreActionViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

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
        if (TYPO3_MODE === 'FE') {
            return parent::renderStatic($arguments, $renderChildrenClosure, $renderingContext);
        }

        return self::renderFrontendLink($renderingContext->getControllerContext()->getUriBuilder(), $arguments);
    }

    /**
     * @param UriBuilder $uriBuilder
     * @param array  $arguments Arguments
     * @return string Rendered link
     */
    protected static function renderFrontendLink($uriBuilder, array $arguments)
    {
        if ($arguments['pageUid'] === null || !intval($arguments['pageUid'])) {
            throw new \Exception('Missing pageUid argument for extbase link generation from BE context. Check your template!');
        }

        if ($arguments['controller'] === null || $arguments['extensionName'] === null || $arguments['pluginName'] === null) {
            throw new \Exception('Missing arguments for extbase link generation from BE context. Check your template!');
        }

       self::buildFrontend($arguments['pageUid']);

        $uri = $uriBuilder->reset()
            ->setTargetPageUid($arguments['pageUid'])
            ->setTargetPageType($arguments['pageType'])
            ->setNoCache((bool) $arguments['noCache'])
            ->setUseCacheHash(!$arguments['noCacheHash'])
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
            ->setAddQueryStringMethod($arguments['addQueryStringMethod'])
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
     * @return array
     */
    protected static function uriFor(
        $actionName = null,
        $controllerArguments = [],
        $controllerName = null,
        $extensionName = null,
        $pluginName = null,
        $format = '',
        array $additionalParams = []
    ) {
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

    /**
     * @param int $pageUid
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected static function buildFrontend($pageUid)
    {
        return \FelixNagel\T3extblog\Utility\GeneralUtility::getTsFe($pageUid);
    }
}
