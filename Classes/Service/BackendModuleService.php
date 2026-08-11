<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Utility\FrontendUtility;
use TYPO3\CMS\Backend\Template\Components\ComponentFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder as MvcUriBuilder;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;

/**
 * BackendModuleService.
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
class BackendModuleService
{
    /**
     * BackendModuleService constructor.
     */
    public function __construct(
        protected ModuleTemplate $moduleTemplate,
        protected ComponentFactory $componentFactory,
        protected int $pid
    ) {
    }

    /**
     * Add doc header meta information
     */
    public function addMetaInformation()
    {
        $permissionClause = $this->getBackendUserAuthentication()->getPagePermsClause(Permission::PAGE_SHOW);
        $pageRecord = BackendUtility::readPageAccess($this->pid, $permissionClause);

        if ($pageRecord) {
            $this->moduleTemplate->getDocHeaderComponent()->setPageBreadcrumb($pageRecord);
        }
    }

    /**
     * Add JS and CSS assets to the view
     */
    public function addViewAssets(array $requireJsModules = [], array $cssLibraries = [])
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        foreach ($requireJsModules as $requireJsModule) {
            $pageRenderer->loadJavaScriptModule($requireJsModule);
        }

        foreach ($cssLibraries as $cssLibrary) {
            $pageRenderer->addCssLibrary($cssLibrary);
        }
    }

    /**
     * Generates the action menu
     *
     */
    public function addViewHeaderMenu(Request $request, array $menuItems, string $menuIdentifier)
    {
        $menu = $this->componentFactory->createMenu();
        $menu->setIdentifier($menuIdentifier);

        $uriBuilder = GeneralUtility::makeInstance(MvcUriBuilder::class);
        $uriBuilder->setRequest($request);

        foreach ($menuItems as $menuItemConfig) {
            $isActive = ($request->getControllerActionName() === $menuItemConfig['action'] &&
                $request->getControllerName() === $menuItemConfig['controller']);
            $menuItem = $this->componentFactory->createMenuItem()
                ->setTitle($menuItemConfig['label'])
                ->setHref($uriBuilder->reset()->uriFor($menuItemConfig['action'], [], $menuItemConfig['controller']))
                ->setActive($isActive);
            $menu->addMenuItem($menuItem);
        }

        $this->moduleTemplate->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /**
     * Create the panel of buttons
     */
    public function addViewHeaderButtons(array $buttonItems, ?string $shortcutModuleName = null)
    {
        $uriBuilder = GeneralUtility::makeInstance(BackendUriBuilder::class);
        $buttonBar = $this->moduleTemplate->getDocHeaderComponent()->getButtonBar();
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        foreach ($buttonItems as $configuration) {
            $parameters = [
                'edit' => [
                    $configuration['table'] => [
                        $this->pid => 'new',
                    ],
                ],
                // @extensionScannerIgnoreLine
                'returnUrl' => FrontendUtility::getNormalizedParams()->getRequestUri(),
            ];
            if (!empty($configuration['defaults'])) {
                $parameters['defVals'] = $configuration['defaults'];
            }

            $viewButton = $this->componentFactory->createLinkButton()
                ->setHref((string)$uriBuilder->buildUriFromRoute('record_edit', $parameters))
                ->setTitle($configuration['label'])
                ->setIcon($iconFactory->getIcon($configuration['icon'], IconSize::SMALL, 'overlay-new'));

            $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, 10);
        }

        // Shortcut
        if ($shortcutModuleName !== null) {
            $shortcutButton = $this->componentFactory->createShortcutButton()
                ->setRouteIdentifier($shortcutModuleName)
                ->setDisplayName('Blog');
            $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }
    }


    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }


    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
