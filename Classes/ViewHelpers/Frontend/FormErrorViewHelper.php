<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Checks a form or field and returns a given string if an error has been found.
 */
class FormErrorViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'for',
            'string',
            'The name of the field (e.g. argument name or property name). This can also be a property path
                (like blog.title), and will then only display the validation errors of that property.'
        );
        $this->registerArgument(
            'error',
            'string',
            'The string to return if the form or field has errors',
            false,
            'has-error'
        );
    }

    public function render()
    {
        /** @var RenderingContext $renderingContext */
        /** @var ExtbaseRequestParameters $extbaseRequestParameters */
        $extbaseRequestParameters = $this->renderingContext
            ->getAttribute(ServerRequestInterface::class)
            ->getAttribute('extbase');
        $validationResults = $extbaseRequestParameters->getOriginalRequestMappingResults();

        $for = $this->arguments['for'];
        $error = $this->arguments['error'];

        if ($validationResults !== null && $for !== '') {
            $validationResults = $validationResults->forProperty($for);
        }

        if ($validationResults->hasErrors()) {
            return $error;
        }

        return '';
    }
}
