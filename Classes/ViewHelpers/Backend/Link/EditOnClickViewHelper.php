<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend\Link;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * A view helper for creating edit on click links.
 */
class EditOnClickViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Arguments initialization.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();

        $this->registerArgument('parameters', 'string', 'Link parameters', true);
        $this->registerArgument('redirectUrl', 'string', 'Redirect URL', false, '');
    }

    /**
     * Returns a link with edit on click handler.
     *
     * @see BackendUtility::editOnClick
     *
     * @return string
     */
    public function render()
    {
        $parameters = $this->arguments['parameters'];
        $redirectUrl = $this->arguments['redirectUrl'];

        // Need to add id for TYPO3 8.x
        $parameters = '&id='.intval(GeneralUtility::_GP('id')).'&'.$parameters;
        $onClickAttribute = BackendUtility::editOnClick('&'.$parameters, '', $redirectUrl);

        $this->tag->addAttribute('href', '#');
        $this->tag->addAttribute('onclick', $onClickAttribute);
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }
}
