<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\ViewHelpers\AbstractConditionViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Information\Typo3Version;

/**
 * ViewHelper to render children only for specific versions.
 */
class Typo3VersionViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('version', 'string', 'Version to match', true, '8.0');
        $this->registerArgument('operator', 'string', 'Compare operator', true, '>');
    }

    protected static function evaluateCondition(?array $arguments = null)
    {
        $version = $arguments['version'];
        $operator = $arguments['operator'];

        return version_compare(GeneralUtility::makeInstance(Typo3Version::class)->getBranch(), (int) $version, $operator);
    }
}
