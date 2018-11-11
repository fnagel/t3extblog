<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2018 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Checks a form or field and returns a given string if an error has been found.
 */
class FormErrorViewHelper extends AbstractViewHelper
{
    /**
     * @inheritdoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'for',
            'string',
            'The name of the field (e.g. argument name or property name). This can also be a property path (like blog.title), and will then only display the validation errors of that property.'
        );
        $this->registerArgument(
            'error',
            'string',
            'The string to return if the form or field has errors',
            false,
            'has-error'
        );
    }

    /**
     * @inheritdoc
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        /* @var $controllerContext ControllerContext */
        $controllerContext = $renderingContext->getcontrollerContext();
        $validationResults = $controllerContext->getRequest()->getOriginalRequestMappingResults();

        $for = $arguments['for'];
        $error = $arguments['error'];

        if ($validationResults !== null && $for !== '') {
            $validationResults = $validationResults->forProperty($for);
        }

        if ($validationResults->hasErrors()) {
            return $error;
        }

        return '';
    }
}
