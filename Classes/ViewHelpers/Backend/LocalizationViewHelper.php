<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2018 Felix Nagel <info@felixnagel.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Show localized posts view helper.
 */
class LocalizationViewHelper extends AbstractBackendViewHelper
{
    use CompileWithRenderStatic;

    /**
     * This view helper renders HTML, thus output must not be escaped
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @var \TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider
     */
    protected static $translateTools;

    /**
     * Contains sys language icons and titles.
     *
     * @var array
     */
    public static $systemLanguages = [];

    /**
     * @inheritdoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('translations', 'string', 'Name of the added variable', true);
        $this->registerArgument('table', 'string', 'Table to process', true);
        $this->registerArgument('object', 'object', 'Object to process', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return mixed
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $translations = $arguments['translations'];
        $table = $arguments['table'];
        $object = $arguments['object'];

        $content = '';
        $templateVariableContainer = $renderingContext->getVariableProvider();

        self::$systemLanguages = self::getTranslateTools()->getSystemLanguages($object->getPid());
        if (count(self::$systemLanguages) > 2) {
            $records = self::getLocalizedRecords($table, $object->toArray());

            $templateVariableContainer->add($translations, $records);
            $content = $renderChildrenClosure();
            $templateVariableContainer->remove($translations);
        }

        return $content;
    }

    /**
     * Creates the localization panel.
     *
     * @param string $table The table
     * @param array  $row   The record for which to make the localization panel.
     *
     * @return array
     */
    public static function getLocalizedRecords($table, $row)
    {
        $records = [];
        $translations = self::$translateTools->translationInfo($table, $row['uid'], 0, $row);

        if (is_array($translations) && is_array($translations['translations'])) {
            foreach ($translations['translations'] as $sysLanguageUid => $translationData) {
                if (!$GLOBALS['BE_USER']->checkLanguageAccess($sysLanguageUid)) {
                    continue;
                }

                if (isset($translations['translations'][$sysLanguageUid])) {
                    $records[$sysLanguageUid] = [
                        'editIcon' => self::getLanguageIconLink(
                            $sysLanguageUid,
                            BackendUtility::editOnClick('&edit['.$table.']['.$translationData['uid'].']=edit'),
                            $translationData['uid']
                        ),
                        'uid' => $translations['translations'][$sysLanguageUid]['uid'],
                    ];
                }
            }
        }

        return $records;
    }

    protected static function getLanguageIconLink($sysLanguageUid, $onclick, $uid)
    {
        $language = BackendUtility::getRecord('sys_language', $sysLanguageUid, 'title');

        if (self::$systemLanguages[$sysLanguageUid]['flagIcon']) {
            /* @var $iconFactory \TYPO3\CMS\Core\Imaging\IconFactory */
            $iconFactory = GeneralUtility::makeInstance(IconFactory::class);
            $icon = $iconFactory->getIcon(self::$systemLanguages[$sysLanguageUid]['flagIcon'], Icon::SIZE_SMALL)->render();
        } else {
            $icon = self::$systemLanguages[$sysLanguageUid]['title'];
        }

        return '<a href="" onclick="'.htmlspecialchars($onclick).'" title="'.$uid.', '.
            htmlspecialchars($language['title']).'">'.$icon.'</a>';
    }

    /**
     * Gets an instance of TranslationConfigurationProvider.
     *
     * @return TranslationConfigurationProvider
     */
    protected static function getTranslateTools()
    {
        if (!isset(self::$translateTools)) {
            self::$translateTools = GeneralUtility::makeInstance(TranslationConfigurationProvider::class);
        }

        return self::$translateTools;
    }
}
