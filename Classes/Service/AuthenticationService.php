<?php

namespace FelixNagel\T3extblog\Service;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2018 Felix Nagel <info@felixnagel.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * AuthenticationService.
 */
class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * Session data.
     *
     * @var array
     */
    protected $sessionData = null;

    /**
     * Session Service.
     *
     * @var \FelixNagel\T3extblog\Service\SessionServiceInterface
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $session;

    /**
     * @return bool
     */
    public function isValid()
    {
        if ($this->getEmail()) {
            return true;
        }

        return false;
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
