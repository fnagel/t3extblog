<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014-2018 Felix Nagel <info@felixnagel.com>
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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use FelixNagel\T3extblog\Utility\GeneralUtility;

/**
 * View helper to fix flash message caching issue
 *
 * https://github.com/fnagel/t3extblog/issues/112
 *
 * Usage:
 *
 * <t3b:flashMessagesClearCache />
 * <f:flashMessages />
 *
 */
class FlashMessagesClearCacheViewHelper extends AbstractViewHelper
{
    /**
     * @inheritdoc
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('queueIdentifier', 'string', 'Flash-message queue to use');
    }

    /**
     * Fix flash message caching
     *
     * @todo Core bug, see https://github.com/fnagel/t3extblog/issues/112
     *
     * @return void
     */
    public function render()
    {
        $queueIdentifier = isset($this->arguments['queueIdentifier']) ? $this->arguments['queueIdentifier'] : null;
        $flashMessages = $this->renderingContext->getControllerContext()->getFlashMessageQueue($queueIdentifier)->getAllMessages();

        if (count($flashMessages) > 0) {
            GeneralUtility::disableFrontendCache();
        }
    }
}
