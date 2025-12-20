<?php
namespace FelixNagel\T3extblog\Mail;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Fluid\View\TemplatePaths;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class FluidEmail extends \TYPO3\CMS\Core\Mail\FluidEmail implements EmailWithViewInterface
{
    public function __construct(
        string $template,
        array $frameworkConfig,
    )
    {
        parent::__construct($this->getTemplatePaths($frameworkConfig['view']));

        $this->setTemplate($template);
    }

    /**
     * Returns an instance of TemplatePaths with paths configured in TypoScript and
     * paths configured in $GLOBALS['TYPO3_CONF_VARS']['MAIL'].
     *
     * Taken from EXT:fe_login.
     */
    protected function getTemplatePaths(array $frameworkViewConfig): TemplatePaths
    {
        $templatePaths = new TemplatePaths();
        $templatePaths->setTemplateRootPaths(array_filter(array_replace(
            $GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'] ?? [],
            $frameworkViewConfig['templateRootPaths'] ?? [],
        )));
        $templatePaths->setLayoutRootPaths(array_filter(array_replace(
            $GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths'] ?? [],
                $frameworkViewConfig['layoutRootPaths'] ?? [],
        )));
        $templatePaths->setPartialRootPaths(array_filter(array_replace(
            $GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths'] ?? [],
                $frameworkViewConfig['partialRootPaths'] ?? [],
        )));

        return $templatePaths;
    }

    public function getView(): ViewInterface
    {
        return $this->view;
    }

    public function getLayout(): string
    {
        return 'SystemEmail';
    }

    public function getRenderingContext(): RenderingContextInterface
    {
        return $this->getView()->getRenderingContext();
    }

    public function assign($key, $value): static
    {
        return parent::assign($key, $value);
    }

    public function assignMultiple(array $values): static
    {
        return parent::assignMultiple($values);
    }

    public function render(string $templateFileName = ''): string
    {
        $this->setTemplate($templateFileName);

        return $this->getBody()->bodyToString();
    }

    public function setTemplate(string $templateName): static
    {
        $this->setFormatByTemplate($templateName);

        return parent::setTemplate(pathinfo($templateName, PATHINFO_BASENAME));
    }

    public function setFormatByTemplate(string $templateName): static
    {
        $this->setFormatByExtension(pathinfo($templateName, PATHINFO_EXTENSION));

        return $this;
    }

    public function setFormatByExtension(string $extension): static
    {
        $this->format(match ($extension) {
            '' => self::FORMAT_BOTH,
            'html' => self::FORMAT_HTML,
            'txt' => self::FORMAT_PLAIN,
            default => throw new \InvalidArgumentException(
                'File extension must be "html" or "txt", no other formats are currently supported', 1580743848
            ),
        });

        return $this;
    }
}
