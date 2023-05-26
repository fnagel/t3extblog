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
    protected ?array $sessionData = null;

    public function __construct(protected SessionServiceInterface $session)
    {
    }

    public function isValid(): bool
    {
        return (bool) $this->getEmail();
    }


    public function login(string $email): bool
    {
        $this->session->setData(
            [
                'email' => $email,
            ]
        );

        return true;
    }


    public function logout()
    {
        $this->session->removeData();
    }

    public function getEmail(): string
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

    protected function getData(): ?array
    {
        if ($this->sessionData === null) {
            $this->sessionData = $this->session->getData();
        }

        return $this->sessionData;
    }
}
