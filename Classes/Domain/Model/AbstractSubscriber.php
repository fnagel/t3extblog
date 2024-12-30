<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Crypto\HashService;
use FelixNagel\T3extblog\Validation\Validator\PrivacyPolicyValidator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * AbstractSubscriber.
 */
abstract class AbstractSubscriber extends AbstractEntity
{
    protected bool $hidden = true;

    protected bool $deleted = false;

    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    #[Extbase\Validate(['validator' => 'EmailAddress'])]
    protected ?string $email = null;

    protected ?\DateTime $lastSent = null;

    protected ?string $code = null;

    #[Extbase\Validate(['validator' => PrivacyPolicyValidator::class, 'options' => ['key' => 'blog']])]
    protected bool $privacyPolicyAccepted = false;

    /**
     * If the subscriber is valid for opt-in email.
     */
    public function isValidForOptin(): bool
    {
        return $this->isHidden() && !$this->deleted && $this->getLastSent() === null;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getLastSent(): ?\DateTime
    {
        return $this->lastSent;
    }

    public function setLastSent(\DateTime $lastSent): void
    {
        $this->lastSent = $lastSent;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    protected function createCode(): void
    {
        $now = new \DateTime();
        /** @noinspection NonSecureUniqidUsageInspection */
        $input = $this->email.$now->getTimestamp().uniqid();

        $this->code = substr(GeneralUtility::makeInstance(HashService::class)->hmac($input, $now->getTimestamp()), 0, 32);
    }

    public function hasPrivacyPolicyAccepted(): bool
    {
        return $this->privacyPolicyAccepted;
    }

    public function setPrivacyPolicyAccepted(bool $privacyPolicyAccepted): void
    {
        $this->privacyPolicyAccepted = $privacyPolicyAccepted;
    }

    /**
     * Update subscriber.
     */
    public function updateAuth(): void
    {
        $this->setLastSent(new \DateTime());
        $this->createCode();
    }

    /**
     * Returns prepared mailto array.
     */
    public function getMailTo(): array
    {
        $mail = [$this->getEmail() => ''];

        if (method_exists($this, 'getName')) {
            $mail = [$this->getEmail() => $this->getName()];
        }

        return $mail;
    }

    /**
     * Checks if the authCode is still valid.
     */
    public function isAuthCodeExpired(string $expireDate): bool
    {
        $now = new \DateTime();
        $expire = clone $this->getLastSent();

        return $now > $expire->modify($expireDate);
    }
}
