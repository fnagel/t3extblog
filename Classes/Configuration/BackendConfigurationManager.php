<?php

namespace FelixNagel\T3extblog\Configuration;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;

class BackendConfigurationManager extends \TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager
{
    /**
     * Make sure TS is generated from currently selected page.
     *
     * Needed basically to make extbase work in some context. Without PID is set to 0
     * (which is root) and persistence, TS generation, etc. will fail. This is the case
     * in TYPO3 8-12 when editing a record.
     *
     * Example: right-click on a record using the context menu (t3js-clickmenutrigger
     * CSS class).in the blog (or list) BE module and use edit (tab access, change
     * to valid and click save) or change visibility directly in context menu which
     * should trigger emails but does not.
     *
     * @todo Rework this: ugly hack but not sure how to solve this in a clean way
     */
    protected function getCurrentPageIdFromRequest(): int
    {
        $id = parent::getCurrentPageIdFromRequest();

        if (empty($id) && $GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) {
            $id = (int)($GLOBALS['TYPO3_REQUEST']->getParsedBody()['popViewId'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['popViewId'] ?? 0);
        }

        return $id;
    }
}
