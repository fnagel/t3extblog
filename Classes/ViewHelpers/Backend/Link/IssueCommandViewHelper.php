<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend\Link;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * A view helper for creating quick-edit links.
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

        $this->registerArgument('parameters', 'string', 'Link parameters', true);
        $this->registerArgument('redirectUrl', 'string', 'Redirect URL', false, '');
    }

    /**
     * Returns a link with a command to TYPO3 Core Engine (tce_db.php).
     */
    public function render(): string
    {
        $parameters = $this->arguments['parameters'];
        $redirectUrl = $this->arguments['redirectUrl'];

        // Needed in 7.x and 8.x
        $request = $GLOBALS['TYPO3_REQUEST'];
        $id = $request->getParsedBody()['id'] ?? $request->getQueryParams()['id'] ?? 0;
        $parameters = '&id='.$id.'&'.$parameters;

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $href = $uriBuilder->buildUriFromRoute('tce_db') . $parameters . '&redirect=';
        // @extensionScannerIgnoreLine
        $href .= rawurlencode($redirectUrl ?: $request->getAttribute('normalizedParams')->getRequestUri());

        $this->tag->addAttribute('href', $href);
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }
}
