<?php

namespace FelixNagel\T3extblog\Event;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

class SendEmailEvent extends AbstractEvent
{
    public function __construct(
        protected array $mailTo,
        protected array $mailFrom,
        protected string $subject,
        protected array $variables,
        protected string $templatePath
    ) {
    }

    public function getMailTo(): array
    {
        return $this->mailTo;
    }

    public function setMailTo(array $mailTo): void
    {
        $this->mailTo = $mailTo;
    }

    public function getMailFrom(): array
    {
        return $this->mailFrom;
    }

    public function setMailFrom(array $mailFrom): void
    {
        $this->mailFrom = $mailFrom;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    public function setTemplatePath(string $templatePath): void
    {
        $this->templatePath = $templatePath;
    }
}
