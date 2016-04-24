<?php

namespace TYPO3\T3extblog\ViewHelpers;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper as BaseAbstractConditionViewHelper;

/**
 * Base for condition VH
 *
 * Includes caching fixes for 7.x & 8.x while maintaining 6.x compatibility
 */
class AbstractConditionViewHelper extends BaseAbstractConditionViewHelper {

	/**
	 *
	 * @inheritdoc
	 */
	public function __construct() {
		// @todo Remove parent call when 7.6 is no longer relevant
		if (is_callable('parent::__construct')) {
			parent::__construct();
		}

		// @todo Remove this when 6.2 is no longer relevant
		if (version_compare(TYPO3_branch, '7.0', '<')) {
			$this->initializeArguments();
		}
	}

	/**
	 * @todo Remove this when 6.2 is no longer relevant
	 *
	 * @inheritdoc
	 */
	public function initializeArguments() {
		// @todo Remove this when 6.2 is no longer relevant
		if (version_compare(TYPO3_branch, '7.0', '>=')) {
			parent::initializeArguments();
		}
	}

	/**
	 * @todo Remove this when 6.2 is no longer relevant
	 *
	 * @inheritdoc
	 */
	public function render() {
		if (static::evaluateCondition($this->arguments)) {
			return $this->renderThenChild();
		} else {
			return $this->renderElseChild();
		}
	}

	/**
	 * Disable caching for 7.x, not called in 6.x
	 *
	 * @inheritdoc
	 */
	public function compile(
		$argumentsVariableName,
		$renderChildrenClosureVariableName,
		&$initializationPhpCode,
		\TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\AbstractNode $syntaxTreeNode,
		\TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler $templateCompiler
	) {
		parent::compile(
			$argumentsVariableName,
			$renderChildrenClosureVariableName,
			$initializationPhpCode,
			$syntaxTreeNode,
			$templateCompiler
		);

		return \TYPO3\CMS\Fluid\Core\Compiler\TemplateCompiler::SHOULD_GENERATE_VIEWHELPER_INVOCATION;
	}
}
