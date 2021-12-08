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
    /**
     * Add data.
     *
     * @param array $data Data array to save
     */
    public function setData(array $data);

    /**
     * Get data.
     *
     */
    public function getData(): array;

    /**
     * Remove data.
     */
    public function removeData();

    /**
     * Get single value from data by key.
     *
     *
     * @return array|string
     */
    public function getDataByKey(string $key);
}
