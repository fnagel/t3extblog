<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Domain\Model\BackendUser as BaseBackendUser;

/**
 * BackendUser.
 */
class BackendUser extends BaseBackendUser
{
    /**
     * Returns the name value.
     *
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
     *
     */
    public function getMailTo(): array
    {
        return [$this->getEmail() => $this->getName()];
    }
}
