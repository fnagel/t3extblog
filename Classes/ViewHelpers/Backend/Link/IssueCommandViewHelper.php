<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend\Link;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015-2018 Felix Nagel <info@felixnagel.com>
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A view helper for creating edit on click links.
 */
class IssueCommandViewHelper extends AbstractTagBasedViewHelper
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
     * Returns a link with a command to TYPO3 Core Engine (tce_db.php).
     *
     * @see DocumentTemplate::issueCommand()
     *
     * @return string
     */
    public function render()
    {
        $parameters = $this->arguments['parameters'];
        $redirectUrl = $this->arguments['redirectUrl'];

        // Needed in 7.x and 8.x
        $parameters = '&id='.intval(GeneralUtility::_GP('id')).'&'.$parameters;
        $href = BackendUtility::getLinkToDataHandlerAction($parameters, $redirectUrl);

        $this->tag->addAttribute('href', $href);
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }
}
