<?php

namespace FelixNagel\T3extblog\ViewHelpers;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Traits\DisableCompilerConditionViewHelperTrait;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper as BaseAbstractConditionViewHelper;

/**
 * Base for condition VH.
 *
 * Includes caching fixes for 8.x
 */
class AbstractConditionViewHelper extends BaseAbstractConditionViewHelper
{
    use DisableCompilerConditionViewHelperTrait;
}
