<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * BackendUser.
 */
class BackendUser extends AbstractEntity
{
    protected string $userName = '';

    protected bool $isAdministrator = false;

    protected bool $isDisabled = false;

    protected string $email = '';

    protected string $realName = '';

    protected ?\DateTime $lastLoginDateAndTime = null;

    /**
     * Returns the name value.
     */
    public function getName(): string
    {
        $name = $this->getRealName();

        if ($name === '') {
            $name = $this->getUserName();
        }

        return $name;
    }

    /**
     * Returns prepared mailto array.
     */
    public function getMailTo(): array
    {
        return [$this->getEmail() => $this->getName()];
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): void
    {
        $this->userName = $userName;
    }

    /**
     * Checks whether this user is an administrator.
     */
    public function getIsAdministrator(): bool
    {
        return $this->isAdministrator;
    }

    /**
     * Checks whether this user is disabled.
     */
    public function getIsDisabled(): bool
    {
        return $this->isDisabled;
    }

    /**
     * Gets the e-mail address of this user.
     *
     * @return string the e-mail address, might be empty
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Returns this user's real name.
     *
     * @return string the real name. might be empty
     */
    public function getRealName(): string
    {
        return $this->realName;
    }

    public function setRealName(string $realName): void
    {
        $this->realName = $realName;
    }
}
