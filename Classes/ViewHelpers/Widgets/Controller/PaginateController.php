<?php

namespace TYPO3\T3extblog\ViewHelpers\Widget\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2015 Felix Nagel <info@felixnagel.com>
 *
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

use TYPO3\CMS\Fluid\ViewHelpers\Widget\Controller\PaginateController as BasePaginateController;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Paginate widget.
 *
 * @todo It seems this overwrite is no longer needed in TYPO3 6.x, see here:
 * http://blog.teamgeist-medien.de/2014/11/typo3-fluid-viewhelper-templates-ueberschreiben-z-b-vom-paginate-widget.html
 */
class PaginateController extends BasePaginateController
{
    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     */
    protected function setViewConfiguration(ViewInterface $view)
    {
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
        $widgetViewHelperClassName = $this->request->getWidgetContext()->getWidgetViewHelperClassName();
        $templateRootPath = $extbaseFrameworkConfiguration['view']['widget'][$widgetViewHelperClassName]['templateRootPath'];

        if (isset($templateRootPath) && strlen($templateRootPath) > 0 && method_exists($view, 'setTemplateRootPath')) {
            $view->setTemplateRootPath(GeneralUtility::getFileAbsFileName($templateRootPath));
        }
    }
}
