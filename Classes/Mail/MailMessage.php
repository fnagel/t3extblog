<?php
namespace FelixNagel\T3extblog\Mail;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mime\Part\AbstractPart;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MailUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class MailMessage extends \TYPO3\CMS\Core\Mail\MailMessage implements EmailWithViewInterface
{
    public function __construct(
        string $template,
        array $frameworkConfig,
        protected ?ViewInterface $view = null,
    )
    {
        parent::__construct();

        if ($this->view === null) {
            $this->view = $this->createStandaloneView($template, $frameworkConfig['view']);
        }
    }

    public function getView(): ViewInterface
    {
        return $this->view;
    }

    public function getLayout(): string
    {
        return 'Email';
    }

    protected function createStandaloneView(string $template, array $frameworkConfig): ViewInterface
    {
        /* @var $viewFactory ViewFactoryInterface */
        $viewFactory = GeneralUtility::makeInstance(ViewFactoryInterface::class);
        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: $frameworkConfig['templateRootPaths'] ?? null,
            partialRootPaths: $frameworkConfig['partialRootPaths'] ?? null,
            layoutRootPaths: $frameworkConfig['layoutRootPaths'] ?? null,
            format: ($format = pathinfo($template, PATHINFO_EXTENSION)) === '' ? 'html' : $format,
        );

        return $viewFactory->create($viewFactoryData);
    }

    public function getRenderingContext(): RenderingContextInterface
    {
        return $this->getView()->getRenderingContext();
    }

    public function setRequest(ServerRequestInterface $request): static
    {
        $this->view->assign('request', $request);
        $this->getRenderingContext()->setAttribute(ServerRequestInterface::class, $request);

        return $this;
    }

    public function assign(string $key, mixed $value): static
    {
        $this->getView()->assign($key, $value);

        return $this;
    }

    public function assignMultiple(array $values): static
    {
        $this->getView()->assignMultiple($values);

        return $this;
    }

    public function ensureValidity(): void
    {
        $this->render();

        parent::ensureValidity();
    }

    public function getBody(): AbstractPart
    {
        $this->render();

        return parent::getBody();
    }

    public function getTextBody()
    {
        if (parent::getTextBody() === null) {
            $this->render();
        }

        return parent::getTextBody();
    }

    public function getHtmlBody()
    {
        if (parent::getHtmlBody() === null) {
            $this->render();
        }

        return parent::getHtmlBody();
    }

    public function render(string $templateFileName = ''): string
    {
        $this->setContent($content = $this->getView()->render());

        return $content;
    }

    protected function setContent(string $emailBody): static
    {
        // Plain text only
        if (strip_tags($emailBody) === $emailBody) {
            $this->text($emailBody);
        } else {
            // Send as HTML and plain text
            $this->html($emailBody);
            $this->text($this->preparePlainTextBody($emailBody));
        }

        return $this;
    }

    /**
     * Prepare HTML as plain text.
     */
    protected function preparePlainTextBody(string $html): string
    {
        // Remove style tags
        $output = preg_replace('#<style\b[^>]*>(.*?)<\/style>#s', '', $html);

        // Remove tags and extract url from link tags
        $output = strip_tags(preg_replace('#(?<=href=["\']).*?(?=["\'])#', '$1', $output));

        // Break lines and clean up white spaces
        $output = MailUtility::breakLinesForEmail($output);

        return preg_replace('#(?:(?:\r\n|\r|\n)\s*){2}#s', "\n\n", $output);
    }
}
