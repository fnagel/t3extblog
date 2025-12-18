<?php

namespace FelixNagel\T3extblog\Traits;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;

/**
 * DisableCompilerConditionViewHelperTrait
 */
trait DisableCompilerConditionViewHelperTrait
{
    /**
     * Disable caching
     *
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function compile(
        $argumentsName,
        $closureName,
        &$initializationPhpCode,
        ViewHelperNode $node,
        TemplateCompiler $compiler
    ): string {
        $compiler->disable();
    }
}
