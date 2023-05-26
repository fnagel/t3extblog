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
    /**
     * @var string
     */
    protected $userName = '';

    /**
     * @var bool
     */
    protected $isAdministrator = false;

    /**
     * @var bool
     */
    protected $isDisabled = false;

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var string
     */
    protected $realName = '';

    /**
     * @var \DateTime|null
     */
    protected $lastLoginDateAndTime;

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

    /**
     * Gets the username.
     *
     * @return string the username, will not be empty
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Checks whether this user is an administrator.
     *
     * @return bool whether this user is an administrator
     */
    public function getIsAdministrator()
    {
        return $this->isAdministrator;
    }

    /**
     * Checks whether this user is disabled.
     *
     * @return bool whether this user is disabled
     */
    public function getIsDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * Gets the e-mail address of this user.
     *
     * @return string the e-mail address, might be empty
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns this user's real name.
     *
     * @return string the real name. might be empty
     */
    public function getRealName()
    {
        return $this->realName;
    }
}
