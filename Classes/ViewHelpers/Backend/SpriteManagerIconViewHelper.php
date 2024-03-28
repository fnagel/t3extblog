<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\ViewHelpers\Be\AbstractBackendViewHelper;

/**
 * Displays sprite icon identified by iconName key.
 */
class SpriteManagerIconViewHelper extends AbstractBackendViewHelper
{
    /**
     * This view helper renders HTML, thus output must not be escaped
     *
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('iconName', 'string', 'Name of the icon', true);
    }

    /**
     * Prints sprite icon html for $iconName key.
     *
     */
    public function render(): string
    {
        $iconName = $this->arguments['iconName'];

        /* @var $iconFactory \TYPO3\CMS\Core\Imaging\IconFactory */
        $iconFactory = GeneralUtility::makeInstance(IconFactory::class);

        return $iconFactory->getIcon($iconName, IconSize::SMALL->value)->render();
    }
}
