<?php

namespace TYPO3\T3extblog\Traits;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Felix Nagel <info@felixnagel.com>
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

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;

/**
 * Includes caching fixes for 8.x
 */
trait ConditionViewHelper8xTrait  {

	/**
	 * Disable caching for TYPO3 8.x
	 *
	 * @inheritdoc
	 */
	public function compile(
		$argumentsName,
		$closureName,
		&$initializationPhpCode,
		ViewHelperNode $node,
		TemplateCompiler $compiler
	) {
		$compiler->disable();

		return $compiler::SHOULD_GENERATE_VIEWHELPER_INVOCATION;
	}
}
