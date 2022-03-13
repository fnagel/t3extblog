<?php

namespace FelixNagel\T3extblog\Domain\Model;

/**
 * This file is part of the "t3extblog" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * PostSubscriber.
 */
class BlogSubscriber extends AbstractSubscriber
{
    public function getSysLanguageUid(): int
    {
        return (int) $this->_languageUid;
    }

    public function setSysLanguageUid(int $language)
    {
        $this->_languageUid = (int) $language;
    }
}
