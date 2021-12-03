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
    /**
     * @var bool
     */
    protected $hidden = true;

    /**
     * @var bool
     */
    protected $deleted = false;

    /**
     * email.
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("EmailAddress")
     */
    protected $email;

    /**
     * lastSent.
     *
     * @var \DateTime
     */
    protected $lastSent = null;

    /**
     * code.
     *
     * @var string
     */
    protected $code;

    /**
     * privacy policy accepted.
     *
     * @var bool
     * @Extbase\Validate("\FelixNagel\T3extblog\Validation\Validator\PrivacyPolicyValidator", options={"key": "blog"})
     */
    protected $privacyPolicyAccepted = false;

    /**
     * If the subscriber is valid for opt in email.
     *
     * @return bool
     */
    public function isValidForOptin()
    {
        return $this->isHidden() && !$this->deleted && $this->getLastSent() === null;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return (bool) $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = (bool) $hidden;
    }

    /**
     * Returns the email.
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the lastSent.
     *
     * @return \DateTime $lastSent
     */
    public function getLastSent()
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sets the code.
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Creates a code.
     */
    protected function createCode()
    {
        $now = new \DateTime();
        $input = $this->email.$now->getTimestamp().uniqid();

        $this->code = substr(GeneralUtility::hmac($input), 0, 32);
    }

    /**
     * @return bool
     */
    public function hasPrivacyPolicyAccepted()
    {
        return $this->privacyPolicyAccepted;
    }

    /**
     * @param bool $privacyPolicyAccepted
     */
    public function setPrivacyPolicyAccepted($privacyPolicyAccepted)
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
     *
     * @return array
     */
    public function getMailTo()
    {
        $mail = [$this->getEmail() => ''];

        if (method_exists($this, 'getName')) {
            $mail = [$this->getEmail() => $this->getName()];
        }

        return $mail;
    }

    /**
     * Checks if the authCode is still valid.
     *
     * @param string $expireDate
     *
     * @return bool
     */
    public function isAuthCodeExpired($expireDate)
    {
        $now = new \DateTime();
        $expire = clone $this->getLastSent();

        return $now > $expire->modify($expireDate);
    }
}
