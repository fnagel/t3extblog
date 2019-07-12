<?php

namespace FelixNagel\T3extblog\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Georg Ringer <typo3@ringerge.org>
 *  (c) 2014-2018 Felix Nagel <info@felixnagel.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TypoScript Utility class.
 *
 * Taken from Georg Ringer's news extension
 */
class TypoScript
{
    /**
     * @param array $base
     * @param array $overload
     *
     * @return array
     */
    public function override(array $base, array $overload)
    {
        $validFields = GeneralUtility::trimExplode(',', $overload['settings']['overrideFlexformSettingsIfEmpty'], true);
        foreach ($validFields as $fieldName) {

            // Multilevel field
            if (strpos($fieldName, '.') !== false) {
                $keyAsArray = explode('.', $fieldName);

                $foundInCurrentTs = $this->getValue($base, $keyAsArray);

                if (is_string($foundInCurrentTs) && strlen($foundInCurrentTs) === 0) {
                    $foundInOriginal = $this->getValue($overload['settings'], $keyAsArray);
                    if ($foundInOriginal) {
                        $base = $this->setValue($base, $keyAsArray, $foundInOriginal);
                    }
                }
            } else {
                // if flexform setting is empty and value is available in TS
                if ((!isset($base[$fieldName]) || (strlen($base[$fieldName]) === 0))
                    && isset($overload['settings'][$fieldName])
                ) {
                    $base[$fieldName] = $overload['settings'][$fieldName];
                }
            }
        }

        return $base;
    }

    /**
     * Get value from array by path.
     *
     * @param array $data
     * @param array $path
     *
     * @return array|null
     */
    protected function getValue(array $data, array $path)
    {
        $found = true;

        for ($x = 0; ($x < count($path) && $found); ++$x) {
            $key = $path[$x];

            if (isset($data[$key])) {
                $data = $data[$key];
            } else {
                $found = false;
            }
        }

        if ($found) {
            return $data;
        }

        return;
    }

    /**
     * Set value in array by path.
     *
     * @param array $array
     * @param $path
     * @param $value
     *
     * @return array
     */
    protected function setValue(array $array, $path, $value)
    {
        $this->setValueByReference($array, $path, $value);

        $final = array_merge_recursive([], $array);

        return $final;
    }

    /**
     * Set value by reference.
     *
     * @param array $array
     * @param array $path
     * @param $value
     */
    private function setValueByReference(array &$array, array $path, $value)
    {
        while (count($path) > 1) {
            $key = array_shift($path);
            if (!isset($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }

        $key = reset($path);
        $array[$key] = $value;
    }
}
