<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\IconSize;
use FelixNagel\T3extblog\Domain\Model\AbstractEntity;
use FelixNagel\T3extblog\Exception\Exception;
use TYPO3\CMS\Backend\Configuration\TranslationConfigurationProvider;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

/**
 * Get localized records view helper.
 */
class LocalizationViewHelper extends AbstractBackendViewHelper
{
    protected $escapeOutput = false;

    protected static ?TranslationConfigurationProvider $translateTools = null;

    /**
     * Contains sys language icons and titles.
     */
    public static array $systemLanguages = [];

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('translations', 'string', 'Name of the added variable', true);
        $this->registerArgument('table', 'string', 'Table to process', true);
        $this->registerArgument('object', 'object', 'Object to process', true);
    }

    public function render(): string
    {
        $translations = $this->arguments['translations'];
        $table = $this->arguments['table'];
        $object = $this->arguments['object'];

        if (!$object instanceof AbstractEntity) {
            throw new Exception('Invalid object given!', 1592862844);
        }

        $content = '';
        $templateVariableContainer = $this->renderingContext->getVariableProvider();
        self::$systemLanguages = self::getTranslateTools()->getSystemLanguages($object->getPid());

        if (count(self::$systemLanguages) > 2) {
            $records = self::getLocalizedRecords($table, $object->toArray());

            $templateVariableContainer->add($translations, $records);
            $content = $this->renderChildren();
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
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public static function getLocalizedRecords(string $table, array $row): array
    {
        $records = [];
        $translations = self::getTranslateTools()->translationInfo($table, $row['uid'], 0, $row);

        if (is_array($translations) && is_array($translations['translations'])) {
            foreach (array_keys($translations['translations']) as $sysLanguageUid) {
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

            return $iconFactory->getIcon(self::$systemLanguages[$sysLanguageUid]['flagIcon'], IconSize::SMALL)->render();
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
