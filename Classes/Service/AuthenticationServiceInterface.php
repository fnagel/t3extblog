<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * AuthenticationServiceInterface.
 */
interface AuthenticationServiceInterface
{
    public function isValid(): bool;


    public function login(string $email): bool;


    public function logout();

    /**
     * Returns email of the subscriber object.
     */
    public function getEmail(): string;
}
