<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend\Uri;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2018 Felix Nagel <info@felixnagel.com>
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

use TYPO3\CMS\Extbase\Service\ExtensionService;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A view helper for creating URIs to extbase actions.
 *
 * This a modfied version of the default extbase class which enables us to
 * use a FE link within a BE context
 *
 * = Examples =
 *
 * <code title="URI to the show-action of the current controller">
 * <f:uri.action action="show" />
 * </code>
 * <output>
 * index.php?id=123&tx_myextension_plugin[action]=show&tx_myextension_plugin[controller]=Standard&cHash=xyz
 * (depending on the current page and your TS configuration)
 * </output>
 */
class ActionViewHelper extends AbstractViewHelper
{
    /**
     * Initialize arguments
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('action', 'string', 'Target action');
        $this->registerArgument('arguments', 'array', 'Arguments', false, []);
        $this->registerArgument('controller', 'string', 'Target controller. If NULL current controllerName is used');
        $this->registerArgument('extensionName', 'string', 'Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used');
        $this->registerArgument('pluginName', 'string', 'Target plugin. If empty, the current plugin name is used');
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination');
        $this->registerArgument('pageType', 'int', 'Type of the target page. See typolink.parameter', false, 0);
        $this->registerArgument('noCache', 'bool', 'Set this to disable caching for the target page. You should not need this.', false, false);
        $this->registerArgument('noCacheHash', 'bool', 'Set this to suppress the cHash query parameter created by TypoLink. You should not need this.', false, false);
        $this->registerArgument('section', 'string', 'The anchor to be added to the URI', false, '');
        $this->registerArgument('format', 'string', 'The requested format, e.g. ".html', false, '');
        $this->registerArgument('linkAccessRestrictedPages', 'bool', 'If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.', false, false);
        $this->registerArgument('additionalParams', 'array', 'additional query parameters that won\'t be prefixed like $arguments (overrule $arguments)', false, []);
        $this->registerArgument('absolute', 'bool', 'If set, an absolute URI is rendered', false, false);
        $this->registerArgument('addQueryString', 'bool', 'If set, the current query parameters will be kept in the URI', false, false);
        $this->registerArgument('argumentsToBeExcludedFromQueryString', 'array', 'arguments to be removed from the URI. Only active if $addQueryString = TRUE', false, []);
        $this->registerArgument('addQueryStringMethod', 'string', 'Set which parameters will be kept. Only active if $addQueryString = TRUE');
    }

    /**
     * @return string Rendered link
     */
    public function render()
    {
        if (TYPO3_MODE === 'FE') {
            return $uri = $this->controllerContext->getUriBuilder()
                ->reset()
                ->setTargetPageUid($this->arguments['pageUid'])
                ->setTargetPageType($this->arguments['pageType'])
                ->setNoCache((bool) $this->arguments['noCache'])
                ->setUseCacheHash(!$this->arguments['noCacheHash'])
                ->setSection($this->arguments['section'])
                ->setFormat($this->arguments['format'])
                ->setLinkAccessRestrictedPages((bool) $this->arguments['linkAccessRestrictedPages'])
                ->setArguments($this->arguments['additionalParams'])
                ->setCreateAbsoluteUri((bool) $this->arguments['absolute'])
                ->setAddQueryString((bool) $this->arguments['addQueryString'])
                ->setArgumentsToBeExcludedFromQueryString($this->arguments['argumentsToBeExcludedFromQueryString'])
                ->setAddQueryStringMethod($this->arguments['addQueryStringMethod'])
                ->uriFor(
                    $this->arguments['action'],
                    $this->arguments['arguments'],
                    $this->arguments['controller'],
                    $this->arguments['extensionName'],
                    $this->arguments['pluginName']
                );
        }

        return $this->renderFrontendLink(
            $this->arguments['action'],
            $this->arguments['arguments'],
            $this->arguments['controller'],
            $this->arguments['extensionName'],
            $this->arguments['pluginName'],
            $this->arguments['pageUid'],
            $this->arguments['pageType'],
            $this->arguments['noCache'],
            $this->arguments['noCacheHash'],
            $this->arguments['section'],
            $this->arguments['format'],
            $this->arguments['linkAccessRestrictedPages'],
            $this->arguments['additionalParams'],
            $this->arguments['absolute'],
            $this->arguments['addQueryString'],
            $this->arguments['argumentsToBeExcludedFromQueryString'],
            $this->arguments['addQueryStringMethod']
        );
    }

    /**
     * @param string $action                               Target action
     * @param array  $arguments                            Arguments
     * @param string $controller                           Target controller. If NULL current controllerName is used
     * @param string $extensionName                        Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used
     * @param string $pluginName                           Target plugin. If empty, the current plugin name is used
     * @param int    $pageUid                              target page. See TypoLink destination
     * @param int    $pageType                             type of the target page. See typolink.parameter
     * @param bool   $noCache                              set this to disable caching for the target page. You should not need this.
     * @param bool   $noCacheHash                          set this to supress the cHash query parameter created by TypoLink. You should not need this.
     * @param string $section                              the anchor to be added to the URI
     * @param string $format                               The requested format, e.g. ".html"
     * @param bool   $linkAccessRestrictedPages            If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.
     * @param array  $additionalParams                     additional query parameters that won't be prefixed like $arguments (overrule $arguments)
     * @param bool   $absolute                             If set, an absolute URI is rendered
     * @param bool   $addQueryString                       If set, the current query parameters will be kept in the URI
     * @param array  $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = TRUE
     * @param string $addQueryStringMethod                 Set which parameters will be kept. Only active if $addQueryString = TRUE
     *
     * @return string Rendered link
     *
     * @throws \Exception
     */
    protected function renderFrontendLink($action = null, array $arguments = [], $controller, $extensionName, $pluginName, $pageUid, $pageType = 0, $noCache = false, $noCacheHash = false, $section = '', $format = '', $linkAccessRestrictedPages = false, array $additionalParams = [], $absolute = false, $addQueryString = false, array $argumentsToBeExcludedFromQueryString = [], $addQueryStringMethod = null)
    {
        if ($pageUid === null || !intval($pageUid)) {
            throw new \Exception('Missing pageUid argument for extbase link generation from BE context. Check your template!');
        }

        if ($controller === null || $extensionName === null || $pluginName === null) {
            throw new \Exception('Missing arguments for extbase link generation from BE context. Check your template!');
        }

        $uriBuilder = $this->controllerContext->getUriBuilder();

        $this->buildFrontend($pageUid);
        $uri = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setTargetPageType($pageType)
            ->setNoCache((bool) $noCache)
            ->setUseCacheHash(!$noCacheHash)
            ->setSection($section)
            ->setFormat($format)
            ->setLinkAccessRestrictedPages((bool) $linkAccessRestrictedPages)
            ->setArguments($this->uriFor($action, $arguments, $controller, $extensionName, $pluginName, $format, $additionalParams))
            ->setCreateAbsoluteUri((bool) $absolute)
            ->setAddQueryString((bool) $addQueryString)
            ->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
            ->setAddQueryStringMethod($addQueryStringMethod)
            ->buildFrontendUri();

        return $uri;
    }

    /**
     * Creates an URI used for linking to an Extbase action.
     * Works in Frontend and Backend mode of TYPO3.
     *
     * @param string $actionName          Name of the action to be called
     * @param array  $controllerArguments Additional query parameters. Will be "namespaced" and merged with $this->arguments.
     * @param string $controllerName      Name of the target controller. If not set, current ControllerName is used.
     * @param string $extensionName       Name of the target extension, without underscores. If not set, current ExtensionName is used.
     * @param string $pluginName          Name of the target plugin. If not set, current PluginName is used.
     * @param string $format              The requested format, e.g. ".html
     * @param array  $additionalParams    additional query parameters that won't be prefixed like $arguments (overrule $arguments)
     *
     * @return string the rendered URI
     *
     * @api
     *
     * @see build()
     */
    public function uriFor($actionName = null, $controllerArguments = [], $controllerName = null, $extensionName = null, $pluginName = null, $format = '', array $additionalParams = [])
    {
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
    protected function buildFrontend($pageUid)
    {
        return \FelixNagel\T3extblog\Utility\GeneralUtility::getTsFe($pageUid);
    }
}
