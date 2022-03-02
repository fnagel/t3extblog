<?php

namespace FelixNagel\T3extblog\Service;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * SessionServiceInterface.
 */
interface SessionServiceInterface
{
    public function setData(array $data);

    public function getData(): ?array;

    public function removeData();

    /**
     * Get single value from data by key.
     */
    public function getDataByKey(string $key): ?array;
}
