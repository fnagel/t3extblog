<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * AuthenticationService.
 */
class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * Session data.
     */
    protected ?array $sessionData = null;

    /**
     * Session Service.
     */
    protected SessionServiceInterface $session;

    /**
     * AuthenticationService constructor.
     *
     * @param SessionServiceInterface $session
     */
    public function __construct(SessionServiceInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return (bool) $this->getEmail();
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function login($email)
    {
        $this->session->setData(
            [
                'email' => $email,
            ]
        );

        return true;
    }

    /**
     *
     */
    public function logout()
    {
        $this->session->removeData();
    }

    /**
     * Returns email of the subscriber object.
     *
     * @return string
     */
    public function getEmail()
    {
        $data = $this->getData();

        if (!(is_array($data) && array_key_exists('email', $data))) {
            return false;
        }

        if (empty($data['email'])) {
            return false;
        }

        return $data['email'];
    }

    /**
     * @return array
     */
    protected function getData()
    {
        if ($this->sessionData === null) {
            $this->sessionData = $this->session->getData();
        }

        return $this->sessionData;
    }
}
