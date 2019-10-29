<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * AbstractLocalizedEntity.
 */
abstract class AbstractLocalizedEntity extends AbstractEntity
{
    /**
     * @return int
     */
    public function getLocalizedUid()
    {
        if ($this->_languageUid) {
            return $this->_localizedUid;
        }

        return $this->getUid();
    }

    /**
     * @return int
     */
    public function getSysLanguageUid()
    {
        return $this->_languageUid;
    }

    /**
     * @return int|null
     */
    public function getL18nParent()
    {
        if ($this->getSysLanguageUid() === 0) {
            return 0;
        }

        return $this->_localizedUid;
    }
}
