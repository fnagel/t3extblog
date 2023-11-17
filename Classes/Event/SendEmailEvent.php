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

    public function getMailFrom(): array
    {
        return $this->mailFrom;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }
}
