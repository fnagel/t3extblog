<?php

namespace FelixNagel\T3extblog\Views;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Views\Factory;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\View\TemplatePaths;

/**
 * DashboardFactory
 */
class DashboardFactory extends Factory
{
    public static function customWidgetTemplate($templateName): StandaloneView
    {
        $view = parent::widgetTemplate();

        $filepath = GeneralUtility::getFileAbsFileName(
            'EXT:t3extblog/' . TemplatePaths::DEFAULT_TEMPLATES_DIRECTORY . 'Widget/' . $templateName . '.html'
        );

        if (file_exists($filepath)) {
            $view->setTemplatePathAndFilename($filepath);
        }

        return $view;
    }
}
