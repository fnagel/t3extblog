<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * AbstractSubscriber.
 */
abstract class AbstractSubscriber extends AbstractEntity
{
    protected bool $hidden = true;

    protected bool $deleted = false;

    /**
     * email.
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("EmailAddress")
     */
    protected ?string $email = null;

    /**
     * lastSent.
     *
     * @var \DateTime
     */
    protected ?\DateTime $lastSent = null;

    /**
     * code.
     *
     * @var string
     */
    protected ?string $code = null;

    /**
     * privacy policy accepted.
     *
     * @Extbase\Validate("\FelixNagel\T3extblog\Validation\Validator\PrivacyPolicyValidator", options={"key": "blog"})
     */
    protected bool $privacyPolicyAccepted = false;

    /**
     * If the subscriber is valid for opt in email.
     */
    public function isValidForOptin(): bool
    {
        return $this->isHidden() && !$this->deleted && $this->getLastSent() === null;
    }

    public function isHidden(): bool
    {
        return (bool) $this->hidden;
    }

    public function setHidden(bool $hidden)
    {
        $this->hidden = (bool) $hidden;
    }

    /**
     * Returns the email.
     *
     * @return string $email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * Returns the lastSent.
     *
     * @return \DateTime $lastSent
     */
    public function getLastSent(): ?\DateTime
    {
        return $this->lastSent;
    }

    /**
     * Sets the lastSent.
     *
     * @param \DateTime|\DateTimeImmutable $lastSent
     */
    public function setLastSent(\DateTimeInterface $lastSent)
    {
        $this->lastSent = $lastSent;
    }

    /**
     * Returns the code.
     *
     * @return string $code
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Sets the code.
     *
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * Creates a code.
     */
    protected function createCode()
    {
        $now = new \DateTime();
        /** @noinspection NonSecureUniqidUsageInspection */
        $input = $this->email.$now->getTimestamp().uniqid();

        $this->code = substr(GeneralUtility::hmac($input), 0, 32);
    }

    public function hasPrivacyPolicyAccepted(): bool
    {
        return $this->privacyPolicyAccepted;
    }

    public function setPrivacyPolicyAccepted(bool $privacyPolicyAccepted)
    {
        $this->privacyPolicyAccepted = $privacyPolicyAccepted;
    }

    /**
     * Update subscriber.
     */
    public function updateAuth()
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
