<?php

namespace FelixNagel\T3extblog\ViewHelpers\Backend\Link;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use FelixNagel\T3extblog\Controller\AbstractBackendController;
use FelixNagel\T3extblog\Utility\FrontendUtility;
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
    public function initializeArguments(): void
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
        parse_str($this->arguments['parameters'], $parameters);

        // The page ID is needed to resolve the TypoScript settings in the DataHandler hook (notification mails)
        $parameters['id'] = AbstractBackendController::getPid(FrontendUtility::getRequest());

        // @extensionScannerIgnoreLine
        $parameters['redirect'] = $this->arguments['redirectUrl'] ?: FrontendUtility::getNormalizedParams()->getRequestUri();

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $href = (string)$uriBuilder->buildUriFromRoute('tce_db', $parameters);

        $this->tag->addAttribute('href', $href);
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }
}
