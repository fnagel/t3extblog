<?php

namespace FelixNagel\T3extblog\ViewHelpers\Frontend;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

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
     */
    public function render(): void
    {
        $queueIdentifier = $this->arguments['queueIdentifier'] ?? null;
        $flashMessages = $this->renderingContext->getControllerContext()->getFlashMessageQueue($queueIdentifier)->getAllMessages();

        if (count($flashMessages) > 0) {
            GeneralUtility::disableFrontendCache();
        }
    }
}
