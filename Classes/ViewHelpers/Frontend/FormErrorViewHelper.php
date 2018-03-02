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

/**
 * Checks a form or field and returns a given string if an error has been found.
 */
class FormErrorViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Iterates through selected errors of the request.
     *
     * @param string $for The name of the error name (e.g. argument name or property name). This can also be a property path (like blog.title), and will then only display the validation errors of that property.
     * @param $error $as The string to return if the form or field has errors
     *
     * @return string Return variable or empty string
     */
    public function render($for = '', $error = 'has-error')
    {
        $validationResults = $this->controllerContext->getRequest()->getOriginalRequestMappingResults();

        if ($validationResults !== null && $for !== '') {
            $validationResults = $validationResults->forProperty($for);
        }

        if ($validationResults->hasErrors()) {
            return $error;
        }

        return '';
    }
}
