<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder as MvcUriBuilder;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * BackendModuleService.
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 */
class BackendModuleService
{
    protected ObjectManagerInterface $objectManager;

    protected BackendTemplateView $view;

    protected int $pid;

    /**
     * BackendModuleService constructor.
     *
     */
    public function __construct(ObjectManagerInterface $objectManager, BackendTemplateView $view, int $pid)
    {
        $this->objectManager = $objectManager;
        $this->view = $view;
        $this->pid = $pid;
    }

    /**
     * Add doc header meta information
     */
    public function addMetaInformation()
    {
        $permissionClause = $this->getBackendUserAuthentication()->getPagePermsClause(Permission::PAGE_SHOW);
        $pageRecord = BackendUtility::readPageAccess($this->pid, $permissionClause);
        if ($pageRecord) {
            $this->view->getModuleTemplate()->getDocHeaderComponent()->setMetaInformation($pageRecord);
        }
    }


    /**
     * Add JS and CSS assets to the view
     *
     */
    public function addViewAssets(array $requireJsModules = [], array $cssLibraries = [])
    {
        $pageRenderer = $this->view->getModuleTemplate()->getPageRenderer();

        foreach ($requireJsModules as $requireJsModule) {
            $pageRenderer->loadRequireJsModule($requireJsModule);
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
        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier($menuIdentifier);

        $uriBuilder = $this->objectManager->get(MvcUriBuilder::class);
        $uriBuilder->setRequest($request);

        foreach ($menuItems as $menuItemConfig) {
            $isActive = ($request->getControllerActionName() === $menuItemConfig['action'] &&
                $request->getControllerName() === $menuItemConfig['controller']);
            $menuItem = $menu->makeMenuItem()
                ->setTitle($menuItemConfig['label'])
                ->setHref($uriBuilder->reset()->uriFor($menuItemConfig['action'], [], $menuItemConfig['controller']))
                ->setActive($isActive);
            $menu->addMenuItem($menuItem);
        }

        $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
    }

    /**
     * Create the panel of buttons
     *
     * @param string $shortcutModuleName
     */
    public function addViewHeaderButtons(array $buttonItems, string $shortcutModuleName = null, bool $addRefreshButton = true)
    {
        $uriBuilder = $this->objectManager->get(BackendUriBuilder::class);
        $buttonBar = $this->view->getModuleTemplate()->getDocHeaderComponent()->getButtonBar();
        $iconFactory = $this->objectManager->get(IconFactory::class);

        foreach ($buttonItems as $configuration) {
            $parameters = [
                'edit' => [
                    $configuration['table'] => [
                        $this->pid => 'new',
                    ],
                ],
                'returnUrl' => GeneralUtility::getIndpEnv('REQUEST_URI'),
            ];
            if (!empty($configuration['defaults'])) {
                $parameters['defVals'] = $configuration['defaults'];
            }

            $viewButton = $buttonBar->makeLinkButton()
                ->setHref((string)$uriBuilder->buildUriFromRoute('record_edit', $parameters))
                ->setTitle($configuration['label'])
                ->setIcon($iconFactory->getIcon($configuration['icon'], Icon::SIZE_SMALL, 'overlay-new'));

            $buttonBar->addButton($viewButton, ButtonBar::BUTTON_POSITION_LEFT, 10);
        }

        // Refresh
        if ($addRefreshButton) {
            $reloadButton = $buttonBar->makeLinkButton()
                ->setHref(GeneralUtility::getIndpEnv('REQUEST_URI'))
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'))
                ->setIcon($iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
            $buttonBar->addButton($reloadButton, ButtonBar::BUTTON_POSITION_RIGHT);
        }

        // Shortcut
        if ($shortcutModuleName !== null) {
            $shortcutButton = $buttonBar->makeShortcutButton()
                ->setModuleName($shortcutModuleName);
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
