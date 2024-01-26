<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Domain\Model\AbstractEntity;
use FelixNagel\T3extblog\Exception\Exception;
use TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * Get localized records view helper.
 */
class LocalizationViewHelper extends AbstractBackendViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    protected static ?TranslationConfigurationProvider $translateTools = null;

    /**
     * Contains sys language icons and titles.
     */
    public static array $systemLanguages = [];

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('translations', 'string', 'Name of the added variable', true);
        $this->registerArgument('table', 'string', 'Table to process', true);
        $this->registerArgument('object', 'object', 'Object to process', true);
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $translations = $arguments['translations'];
        $table = $arguments['table'];
        $object = $arguments['object'];

        if (!$object instanceof AbstractEntity) {
            throw new Exception('Invalid object given!', 1592862844);
        }

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
     */
    public static function getLocalizedRecords(string $table, array $row): array
    {
        $records = [];
        $translations = self::getTranslateTools()->translationInfo($table, $row['uid'], 0, $row);

        if (is_array($translations) && is_array($translations['translations'])) {
            foreach ($translations['translations'] as $sysLanguageUid) {
                if (!$GLOBALS['BE_USER']->checkLanguageAccess($sysLanguageUid)) {
                    continue;
                }

                if (isset($sysLanguageUid) && isset($translations['translations'][$sysLanguageUid])) {
                    $records[$sysLanguageUid] = [
                        'icon' => self::getLanguageIcon($sysLanguageUid),
                        'uid' => $translations['translations'][$sysLanguageUid]['uid'],
                    ];
                }
            }
        }

        return $records;
    }

    protected static function getLanguageIcon($sysLanguageUid): string
    {
        if (self::$systemLanguages[$sysLanguageUid]['flagIcon']) {
            /* @var $iconFactory IconFactory */
            $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

            return $iconFactory->getIcon(self::$systemLanguages[$sysLanguageUid]['flagIcon'], Icon::SIZE_SMALL)->render();
        }

        return self::$systemLanguages[$sysLanguageUid]['title'];
    }

    /**
     * Gets an instance of TranslationConfigurationProvider.
     */
    protected static function getTranslateTools(): TranslationConfigurationProvider
    {
        if (!isset(self::$translateTools)) {
            self::$translateTools = GeneralUtility::makeInstance(TranslationConfigurationProvider::class);
        }

        return self::$translateTools;
    }
}
