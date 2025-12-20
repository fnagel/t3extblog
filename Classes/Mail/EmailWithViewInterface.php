<?php
namespace FelixNagel\T3extblog\Mail;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

interface EmailWithViewInterface extends ViewInterface
{
    public function getView(): ViewInterface;

    public function getLayout(): string;

    public function getRenderingContext(): RenderingContextInterface;

    public function setRequest(ServerRequestInterface $request): static;
}
