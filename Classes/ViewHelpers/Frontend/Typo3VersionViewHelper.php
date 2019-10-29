<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\ViewHelpers\AbstractConditionViewHelper;

/**
 * ViewHelper to render children only for specific versions.
 */
class Typo3VersionViewHelper extends AbstractConditionViewHelper
{
    /**
     * {@inheritdoc}
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('version', 'string', 'Version to match', true, '8.0');
        $this->registerArgument('operator', 'string', 'Compare operator', true, '>');
    }

    /**
     * {@inheritdoc}
     */
    protected static function evaluateCondition($arguments = null)
    {
        $version = $arguments['version'];
        $operator = $arguments['operator'];

        if (version_compare(TYPO3_branch, (int) $version, $operator)) {
            return true;
        }

        return false;
    }
}
